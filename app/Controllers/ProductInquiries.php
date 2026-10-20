<?php

namespace App\Controllers;

class ProductInquiries extends BaseController
{
    public function create()
    {
        $session = session();
        $token = (string) $this->request->getHeaderLine('X-CSRF-Token');
        $sessionToken = (string) $session->get('product_inquiry_csrf_token');

        if ($sessionToken === '' || ! hash_equals($sessionToken, $token)) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Your inquiry session expired. Refresh the page and try again.']);
        }

        $data = $this->request->getJSON(true);
        if (! is_array($data)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Invalid inquiry request.']);
        }

        $clean = [
            'product_id' => (int) ($data['productId'] ?? 0),
            'customer_name' => trim((string) ($data['name'] ?? '')),
            'email' => strtolower(trim((string) ($data['email'] ?? ''))),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'project_address' => trim((string) ($data['address'] ?? '')),
            'message' => trim((string) ($data['message'] ?? '')),
        ];

        $errors = [];
        if ($clean['product_id'] <= 0) $errors['productId'] = 'Choose a valid product.';
        if ($clean['customer_name'] === '' || strlen($clean['customer_name']) > 150) $errors['name'] = 'Enter your full name.';
        if (! filter_var($clean['email'], FILTER_VALIDATE_EMAIL) || strlen($clean['email']) > 254) $errors['email'] = 'Enter a valid email address.';
        if (! preg_match('/^[0-9+()\s-]{7,32}$/', $clean['phone'])) $errors['phone'] = 'Enter a valid phone number.';
        if (strlen($clean['project_address']) > 500) $errors['address'] = 'Address must be 500 characters or fewer.';
        if (strlen($clean['message']) > 1500) $errors['message'] = 'Message must be 1,500 characters or fewer.';

        $product = db_connect()->table('products')->select('id')->where('id', $clean['product_id'])->where('is_published', 1)->get()->getRowArray();
        if ($product === null) $errors['productId'] = 'This product is no longer available for inquiry.';

        if ($errors !== []) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Please correct the highlighted fields.', 'errors' => $errors]);
        }

        db_connect()->table('product_inquiries')->insert([
            'product_id' => $clean['product_id'],
            'user_id' => $session->get('auth_role') === 'customer' ? (int) $session->get('auth_user_id') : null,
            'customer_name' => $clean['customer_name'],
            'email' => $clean['email'],
            'phone' => $clean['phone'],
            'project_address' => $clean['project_address'] !== '' ? $clean['project_address'] : null,
            'message' => $clean['message'] !== '' ? $clean['message'] : null,
        ]);

        $session->remove('product_inquiry_csrf_token');

        return $this->response->setStatusCode(201)->setJSON(['message' => 'Your inquiry is now with the Sun Son Solar team. We will contact you about the next step.']);
    }

    public function updateStatus(int $inquiryId)
    {
        $session = session();
        if (! $this->canManageInquiries() || ! $this->hasDashboardToken()) {
            return redirect()->to(site_url('login'));
        }

        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['new', 'reviewing', 'quoted', 'closed'], true)) {
            return redirect()->to(site_url('dashboard'))->with('portal_error', 'Choose a valid inquiry status.');
        }

        $builder = db_connect()->table('product_inquiries')->where('id', $inquiryId);
        if (session()->get('auth_role') === 'employee') {
            $builder->where('assigned_department', 'Marketing Sales');
        }
        $builder->update(['status' => $status]);

        return redirect()->to(site_url('dashboard') . '#inquiries')->with('portal_success', 'Inquiry status updated.');
    }

    private function hasDashboardToken(): bool
    {
        $sessionToken = (string) session()->get('dashboard_csrf_token');
        $token = (string) $this->request->getPost('csrfToken');

        return $sessionToken !== '' && hash_equals($sessionToken, $token);
    }

    private function canManageInquiries(): bool
    {
        $session = session();
        if ($session->get('auth_role') === 'admin') {
            return true;
        }
        if ($session->get('auth_role') !== 'employee') {
            return false;
        }
        return db_connect()->table('employees')->where('user_id', (int) $session->get('auth_user_id'))->where('department', 'Marketing Sales')->countAllResults() === 1;
    }
}
