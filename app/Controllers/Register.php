<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use DateTimeImmutable;
use Throwable;

class Register extends BaseController
{
    private const DEPARTMENTS = [
        'Installation',
        'Maintenance and Repair',
        'System Design',
        'Sales and Consultation',
        'Administration',
    ];

    public function index(): string
    {
        helper('url');

        return view('register');
    }

    public function csrf()
    {
        $session = session();

        if (! $session->has('registration_csrf_token')) {
            $session->set('registration_csrf_token', bin2hex(random_bytes(32)));
        }

        return $this->response->setJSON([
            'csrfToken' => $session->get('registration_csrf_token'),
        ]);
    }

    public function create()
    {
        $session = session();
        $submittedToken = (string) $this->request->getHeaderLine('X-CSRF-Token');
        $sessionToken = (string) $session->get('registration_csrf_token');

        if ($sessionToken === '' || ! hash_equals($sessionToken, $submittedToken)) {
            return $this->jsonError(403, 'Your session expired. Refresh the page and try again.');
        }

        $data = $this->request->getJSON(true);
        if (! is_array($data)) {
            return $this->jsonError(400, 'Invalid registration payload.');
        }

        $clean = $this->cleanInput($data);
        $errors = $this->validateInput($clean);

        if ($errors !== []) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'message' => 'Please correct the highlighted fields.',
                    'errors' => $errors,
                ]);
        }

        $users = new UserModel();

        if ($users->where('email', $clean['email'])->first() !== null) {
            return $this->jsonFieldError(409, 'email', 'That email address is already registered.');
        }

        if ($users->where('username', $clean['username'])->first() !== null) {
            return $this->jsonFieldError(409, 'username', 'That username is already taken.');
        }

        $db = db_connect();
        $db->transStart();

        try {
            $userId = $users->insert([
                'email' => $clean['email'],
                'username' => $clean['username'],
                'password_hash' => password_hash($clean['password'], PASSWORD_DEFAULT),
                'role' => $clean['role'],
                'account_status' => $clean['role'] === 'employee' ? 'pending' : 'active',
            ], true);

            $profile = [
                'user_id' => $userId,
                'first_name' => $clean['firstName'],
                'middle_name' => $clean['middleName'] !== '' ? $clean['middleName'] : null,
                'last_name' => $clean['lastName'],
                'birthdate' => $clean['birthdate'],
                'gender' => $clean['gender'],
                'phone' => $clean['phone'],
                'address' => $clean['address'],
            ];

            if ($clean['role'] === 'employee') {
                $profile['department'] = $clean['department'];
                (new EmployeeModel())->insert($profile);
            } else {
                (new CustomerModel())->insert($profile);
            }

            $db->transComplete();

            if (! $db->transStatus()) {
                throw new \RuntimeException('Database transaction failed.');
            }
        } catch (Throwable $exception) {
            $db->transRollback();
            log_message('error', 'Registration database error: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->jsonError(500, 'Registration is temporarily unavailable. Please try again later.');
        }

        $session->remove('registration_csrf_token');

        $message = $clean['role'] === 'employee'
            ? 'Your employee registration was submitted for approval.'
            : 'Your account has been created. You can now sign in.';

        return $this->response
            ->setStatusCode(201)
            ->setJSON(['message' => $message]);
    }

    private function cleanInput(array $data): array
    {
        return [
            'firstName' => $this->textValue($data, 'firstName'),
            'middleName' => $this->textValue($data, 'middleName'),
            'lastName' => $this->textValue($data, 'lastName'),
            'birthdate' => $this->textValue($data, 'birthdate'),
            'gender' => $this->textValue($data, 'gender'),
            'role' => $this->textValue($data, 'role'),
            'department' => $this->textValue($data, 'department'),
            'email' => strtolower($this->textValue($data, 'email')),
            'phone' => $this->textValue($data, 'phone'),
            'address' => $this->textValue($data, 'address'),
            'username' => $this->textValue($data, 'username'),
            'password' => (string) ($data['password'] ?? ''),
        ];
    }

    private function validateInput(array $data): array
    {
        $errors = [];

        if ($data['firstName'] === '' || strlen($data['firstName']) > 100) {
            $errors['firstName'] = 'Enter a first name up to 100 characters.';
        }

        if (strlen($data['middleName']) > 100) {
            $errors['middleName'] = 'Middle name must be 100 characters or fewer.';
        }

        if ($data['lastName'] === '' || strlen($data['lastName']) > 100) {
            $errors['lastName'] = 'Enter a last name up to 100 characters.';
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $data['birthdate']);
        if (! $date || $date->format('Y-m-d') !== $data['birthdate'] || $date > new DateTimeImmutable('today')) {
            $errors['birthdate'] = 'Enter a valid birthdate.';
        }

        if (! in_array($data['gender'], ['Female', 'Male', 'Other'], true)) {
            $errors['gender'] = 'Select a valid gender.';
        }

        if (! in_array($data['role'], ['customer', 'employee'], true)) {
            $errors['role'] = 'Select a valid account type.';
        }

        if ($data['role'] === 'employee' && ! in_array($data['department'], self::DEPARTMENTS, true)) {
            $errors['department'] = 'Select a valid department.';
        }

        if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 254) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if (! preg_match('/^[0-9+()\s-]{7,32}$/', $data['phone'])) {
            $errors['phone'] = 'Enter a valid phone number.';
        }

        if ($data['address'] === '' || strlen($data['address']) > 500) {
            $errors['address'] = 'Enter an address up to 500 characters.';
        }

        if (! preg_match('/^[A-Za-z0-9._-]{3,50}$/', $data['username'])) {
            $errors['username'] = 'Use 3-50 letters, numbers, dots, underscores or hyphens.';
        }

        if (strlen($data['password']) < 8 || strlen($data['password']) > 255) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        return $errors;
    }

    private function textValue(array $data, string $key): string
    {
        return trim((string) ($data[$key] ?? ''));
    }

    private function jsonFieldError(int $status, string $field, string $message)
    {
        return $this->response
            ->setStatusCode($status)
            ->setJSON([
                'message' => $message,
                'errors' => [$field => $message],
            ]);
    }

    private function jsonError(int $status, string $message)
    {
        return $this->response
            ->setStatusCode($status)
            ->setJSON(['message' => $message]);
    }
}
