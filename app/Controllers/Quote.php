<?php

namespace App\Controllers;

use App\Models\ServiceRequestModel;

class Quote extends BaseController
{
    public function csrf()
    {
        $session = session();

        if (! $session->has('quote_csrf_token')) {
            $session->set('quote_csrf_token', bin2hex(random_bytes(32)));
        }

        return $this->response->setJSON([
            'csrfToken' => $session->get('quote_csrf_token'),
        ]);
    }

    public function create()
    {
        $submittedToken = (string) $this->request->getHeaderLine('X-CSRF-Token');
        $sessionToken = (string) session()->get('quote_csrf_token');

        if ($sessionToken === '' || ! hash_equals($sessionToken, $submittedToken)) {
            return $this->response->setStatusCode(403)->setJSON([
                'message' => 'Your session expired. Refresh the page and try again.',
            ]);
        }

        $data = $this->request->getJSON(true);

        if (! is_array($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Invalid quote request.',
            ]);
        }

        $clean = [
            'customer_name' => trim((string) ($data['name'] ?? '')),
            'email' => strtolower(trim((string) ($data['email'] ?? ''))),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'address' => trim((string) ($data['address'] ?? '')),
            'service' => trim((string) ($data['service'] ?? '')),
            'preferred_date' => trim((string) ($data['preferredDate'] ?? '')),
            'details' => trim((string) ($data['details'] ?? '')),
        ];

        $errors = [];
        if ($clean['customer_name'] === '' || strlen($clean['customer_name']) > 150) {
            $errors['name'] = 'Enter your full name.';
        }
        if (! filter_var($clean['email'], FILTER_VALIDATE_EMAIL) || strlen($clean['email']) > 254) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if (! preg_match('/^[0-9+()\s-]{7,32}$/', $clean['phone'])) {
            $errors['phone'] = 'Enter a valid phone number.';
        }
        if ($clean['address'] === '' || strlen($clean['address']) > 500) {
            $errors['address'] = 'Enter the service address.';
        }
        if (! in_array($clean['service'], ['consultation', 'installation', 'maintenance'], true)) {
            $errors['service'] = 'Choose a service.';
        }
        if ($clean['preferred_date'] !== '') {
            $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $clean['preferred_date']);
            if (! $date || $date->format('Y-m-d') !== $clean['preferred_date'] || $date < new \DateTimeImmutable('today')) {
                $errors['preferredDate'] = 'Choose a future preferred date.';
            }
        }
        if (strlen($clean['details']) > 1500) {
            $errors['details'] = 'Details must be 1,500 characters or fewer.';
        }

        if ($errors !== []) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Please check the highlighted fields.',
                'errors' => $errors,
            ]);
        }

        $category = db_connect()->table('service_categories')
            ->select('id')
            ->where('code', $clean['service'])
            ->get()
            ->getRowArray();

        if ($category === null) {
            return $this->response->setStatusCode(503)->setJSON([
                'message' => 'Service categories are not ready. Please try again shortly.',
            ]);
        }

        (new ServiceRequestModel())->insert([
            'category_id' => $category['id'],
            'customer_name' => $clean['customer_name'],
            'email' => $clean['email'],
            'phone' => $clean['phone'],
            'address' => $clean['address'],
            'preferred_date' => $clean['preferred_date'] !== '' ? $clean['preferred_date'] : null,
            'details' => $clean['details'] !== '' ? $clean['details'] : null,
            'status' => 'pending',
        ]);

        session()->remove('quote_csrf_token');

        return $this->response->setStatusCode(201)->setJSON([
            'message' => 'Your request was received. Sun Son Solar will contact you to confirm the next step.',
        ]);
    }
}
