<?php

namespace App\Controllers;

class DepartmentMessages extends BaseController
{
    private const DEPARTMENTS = ['Administration', 'IT', 'Despatch', 'Accounting', 'HR', 'Marketing Sales', 'Customer Service'];

    public function create()
    {
        $session = session();
        $userId = (int) $session->get('auth_user_id');
        $role = (string) $session->get('auth_role');
        if ($userId <= 0 || ! in_array($role, ['employee', 'admin'], true) || ! $this->hasDashboardToken()) {
            return redirect()->to(site_url('login'));
        }

        $recipient = trim((string) $this->request->getPost('recipientDepartment'));
        $subject = trim((string) $this->request->getPost('subject'));
        $body = trim((string) $this->request->getPost('body'));
        if (! in_array($recipient, [...self::DEPARTMENTS, 'All departments'], true) || $subject === '' || strlen($subject) > 180 || $body === '' || strlen($body) > 4000) {
            return redirect()->to(site_url('dashboard'))->withInput()->with('portal_error', 'Enter a department, a short subject, and a message up to 4,000 characters.');
        }

        $department = $role === 'admin' ? 'Administration' : (string) db_connect()->table('employees')->select('department')->where('user_id', $userId)->get()->getRow('department');
        if ($department === '') {
            return redirect()->to(site_url('dashboard'))->with('portal_error', 'Your employee department could not be found.');
        }

        db_connect()->table('department_messages')->insert([
            'sender_user_id' => $userId,
            'sender_department' => $department,
            'recipient_department' => $recipient,
            'subject' => $subject,
            'body' => $body,
        ]);

        return redirect()->to(site_url('dashboard') . '#communication')->with('portal_success', 'Message sent to ' . $recipient . '.');
    }

    private function hasDashboardToken(): bool
    {
        $sessionToken = (string) session()->get('dashboard_csrf_token');
        $token = (string) $this->request->getPost('csrfToken');

        return $sessionToken !== '' && hash_equals($sessionToken, $token);
    }
}
