<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        $userId = (int) $session->get('auth_user_id');
        $role = (string) $session->get('auth_role');

        if ($userId <= 0 || ! in_array($role, ['customer', 'employee', 'admin'], true)) {
            $session->destroy();
            return redirect()->to(site_url('login'));
        }

        $db = db_connect();
        $this->touchPresence($userId);
        $department = $this->departmentFor($userId, $role);
        $dashboardToken = bin2hex(random_bytes(32));
        $session->set('dashboard_csrf_token', $dashboardToken);

        $data = [
            'name' => (string) $session->get('auth_name'),
            'role' => $role,
            'department' => $department,
            'summary' => $this->summaryFor($userId, $role, $department),
            'logoutToken' => (string) $session->get('logout_csrf_token'),
            'dashboardToken' => $dashboardToken,
            'departmentMessages' => $this->messagesFor($userId, $role, $department),
            'activeDirectory' => $this->activeDirectory(),
            'departmentOptions' => ['Administration', 'IT', 'Despatch', 'Accounting', 'HR', 'Marketing Sales', 'Customer Service', 'All departments'],
            'customerInquiries' => $role === 'customer' ? $this->customerInquiries($userId) : [],
            'departmentInquiries' => $role === 'employee' && $department === 'Marketing Sales' ? $this->departmentInquiries($department) : [],
            'assignedJobs' => $role === 'employee' ? $this->assignedJobs($userId) : [],
            'departmentMetrics' => $role === 'employee' ? $this->departmentMetrics($userId, $department) : [],
            'analytics' => $this->analytics(),
        ];

        if ($role === 'admin') {
            $staffToken = bin2hex(random_bytes(32));
            $session->set('staff_csrf_token', $staffToken);
            $data['staffToken'] = $staffToken;
            $data['staffAccounts'] = $db->table('users')
                ->select('users.id, users.email, users.username, users.account_status, employees.first_name, employees.last_name, employees.department')
                ->join('employees', 'employees.user_id = users.id')
                ->where('users.role', 'employee')
                ->orderBy('employees.first_name', 'ASC')
                ->orderBy('employees.last_name', 'ASC')
                ->get()
                ->getResultArray();
            $data['allInquiries'] = $this->departmentInquiries(null);
        }

        return view('dashboard', $data);
    }

    private function summaryFor(int $userId, string $role, string $department): array
    {
        $db = db_connect();
        if ($role === 'customer') return [
            'primaryLabel' => 'Scheduled services',
            'primaryValue' => $db->table('service_requests')->where('user_id', $userId)->whereIn('status', ['approved', 'scheduled', 'in_progress'])->countAllResults(),
            'secondaryLabel' => 'Product inquiries',
            'secondaryValue' => $db->table('product_inquiries')->where('user_id', $userId)->countAllResults(),
            'focusTitle' => 'Your service space is ready.',
            'focusText' => 'Track your product inquiries and service updates from one secure account.',
            'actionLabel' => 'Explore products',
            'actionUrl' => site_url('products'),
        ];
        if ($role === 'employee') return [
            'primaryLabel' => 'Assigned services',
            'primaryValue' => $db->table('service_requests')->where('assigned_employee_id', $userId)->whereIn('status', ['approved', 'scheduled', 'in_progress'])->countAllResults(),
            'secondaryLabel' => 'Department messages',
            'secondaryValue' => count($this->messagesFor($userId, $role, $department)),
            'focusTitle' => 'Your department workspace is connected.',
            'focusText' => 'Use the shared channel to coordinate with the right team without moving the work to email or chat apps.',
            'actionLabel' => 'Open communication',
            'actionUrl' => '#communication',
        ];
        return [
            'primaryLabel' => 'New product inquiries',
            'primaryValue' => $db->table('product_inquiries')->where('status', 'new')->countAllResults(),
            'secondaryLabel' => 'Pending service requests',
            'secondaryValue' => $db->table('service_requests')->where('status', 'pending')->countAllResults(),
            'focusTitle' => 'Operations overview is ready.',
            'focusText' => 'Review inquiries, staff activity, customer requests, and department communication from one operations workspace.',
            'actionLabel' => 'Review inquiries',
            'actionUrl' => '#inquiries',
        ];
    }

    private function departmentFor(int $userId, string $role): string
    {
        if ($role === 'admin') return 'Administration';
        if ($role !== 'employee') return '';
        return (string) (db_connect()->table('employees')->select('department')->where('user_id', $userId)->get()->getRow('department') ?? '');
    }

    private function touchPresence(int $userId): void
    {
        db_connect()->query('INSERT INTO user_presence (user_id, last_seen_at) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE last_seen_at = VALUES(last_seen_at)', [$userId]);
    }

    private function messagesFor(int $userId, string $role, string $department): array
    {
        $builder = db_connect()->table('department_messages dm')->select('dm.*, u.username AS sender_username')->join('users u', 'u.id = dm.sender_user_id')->orderBy('dm.created_at', 'DESC')->limit(12);
        if ($role === 'admin') return $builder->get()->getResultArray();
        return $builder->groupStart()->where('dm.recipient_department', $department)->orWhere('dm.recipient_department', 'All departments')->orWhere('dm.sender_user_id', $userId)->groupEnd()->get()->getResultArray();
    }

    private function activeDirectory(): array
    {
        return db_connect()->table('users u')->select("u.id, u.username, u.role, e.first_name, e.last_name, e.department, up.last_seen_at, CASE WHEN up.last_seen_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 1 ELSE 0 END AS is_online", false)->join('employees e', 'e.user_id = u.id', 'left')->join('user_presence up', 'up.user_id = u.id', 'left')->where('u.account_status', 'active')->whereIn('u.role', ['employee', 'admin'])->orderBy('is_online', 'DESC')->orderBy('e.department', 'ASC')->limit(16)->get()->getResultArray();
    }

    private function customerInquiries(int $userId): array
    {
        return db_connect()->table('product_inquiries pi')->select('pi.*, p.name AS product_name')->join('products p', 'p.id = pi.product_id')->where('pi.user_id', $userId)->orderBy('pi.created_at', 'DESC')->get()->getResultArray();
    }

    private function departmentInquiries(?string $department): array
    {
        $builder = db_connect()->table('product_inquiries pi')->select('pi.*, p.name AS product_name')->join('products p', 'p.id = pi.product_id')->orderBy('pi.created_at', 'DESC')->limit(20);
        if ($department !== null) $builder->where('pi.assigned_department', $department);
        return $builder->get()->getResultArray();
    }

    private function analytics(): array
    {
        $db = db_connect();
        return [
            'inquiriesToday' => $db->table('product_inquiries')->where('created_at >=', date('Y-m-d 00:00:00'))->countAllResults(),
            'openInquiries' => $db->table('product_inquiries')->whereIn('status', ['new', 'reviewing'])->countAllResults(),
            'activeEmployees' => $db->table('users')->where('role', 'employee')->where('account_status', 'active')->countAllResults(),
            'openTickets' => $db->table('support_tickets')->whereIn('status', ['open', 'in_progress'])->countAllResults(),
        ];
    }

    private function assignedJobs(int $userId): array
    {
        return db_connect()->table('service_requests sr')
            ->select('sr.id, sr.customer_name, sr.address, sr.status, sr.scheduled_at, sc.name AS category_name')
            ->join('service_categories sc', 'sc.id = sr.category_id')
            ->where('sr.assigned_employee_id', $userId)
            ->whereNotIn('sr.status', ['declined', 'cancelled', 'completed'])
            ->orderBy('sr.scheduled_at', 'ASC')
            ->orderBy('sr.created_at', 'DESC')
            ->get()->getResultArray();
    }

    private function departmentMetrics(int $userId, string $department): array
    {
        $db = db_connect();
        return [
            'assignedJobs' => $db->table('service_requests')->where('assigned_employee_id', $userId)->whereNotIn('status', ['declined', 'cancelled', 'completed'])->countAllResults(),
            'submittedReports' => $db->table('technician_service_reports')->where('employee_user_id', $userId)->countAllResults(),
            'departmentMembers' => $db->table('employees e')->join('users u', 'u.id = e.user_id')->where('e.department', $department)->where('u.account_status', 'active')->countAllResults(),
            'departmentMessages' => $db->table('department_messages')->groupStart()->where('recipient_department', $department)->orWhere('recipient_department', 'All departments')->groupEnd()->countAllResults(),
        ];
    }
}
