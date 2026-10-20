<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\UserModel;
use DateTimeImmutable;
use Throwable;

class StaffAccounts extends BaseController
{
    private const DEPARTMENTS = [
        'Administration',
        'IT',
        'Despatch',
        'Accounting',
        'HR',
        'Marketing Sales',
        'Customer Service',
    ];

    public function create()
    {
        $session = session();

        if ($session->get('auth_role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        $submittedToken = (string) $this->request->getPost('csrfToken');
        $sessionToken = (string) $session->get('staff_csrf_token');
        if ($sessionToken === '' || ! hash_equals($sessionToken, $submittedToken)) {
            return redirect()->to(site_url('dashboard'))->with('staff_error', 'Your staff form session expired. Please try again.');
        }

        $data = $this->cleanInput();
        $errors = $this->validateInput($data);
        if ($errors !== []) {
            return redirect()->to(site_url('dashboard'))->withInput()->with('staff_error', implode(' ', $errors));
        }

        $users = new UserModel();
        if ($users->where('email', $data['email'])->first() !== null) {
            return redirect()->to(site_url('dashboard'))->withInput()->with('staff_error', 'That email address is already in use.');
        }

        if ($users->where('username', $data['username'])->first() !== null) {
            return redirect()->to(site_url('dashboard'))->withInput()->with('staff_error', 'That username is already in use.');
        }

        $db = db_connect();
        $db->transStart();

        try {
            $userId = $users->insert([
                'email' => $data['email'],
                'username' => $data['username'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => 'employee',
                'account_status' => $data['accountStatus'],
            ], true);

            (new EmployeeModel())->insert([
                'user_id' => $userId,
                'first_name' => $data['firstName'],
                'middle_name' => $data['middleName'] !== '' ? $data['middleName'] : null,
                'last_name' => $data['lastName'],
                'birthdate' => $data['birthdate'],
                'gender' => $data['gender'],
                'department' => $data['department'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);

            $db->transComplete();
            if (! $db->transStatus()) {
                throw new \RuntimeException('Database transaction failed.');
            }
        } catch (Throwable $exception) {
            $db->transRollback();
            log_message('error', 'Staff account creation failed: {message}', ['message' => $exception->getMessage()]);

            return redirect()->to(site_url('dashboard'))->withInput()->with('staff_error', 'The employee account could not be created. Please try again.');
        }

        $session->remove('staff_csrf_token');

        return redirect()->to(site_url('dashboard'))->with('staff_success', 'Employee account created. The employee can sign in when the account status is active.');
    }

    public function update(int $userId)
    {
        $session = session();

        if ($session->get('auth_role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        $submittedToken = (string) $this->request->getPost('csrfToken');
        $sessionToken = (string) $session->get('staff_csrf_token');
        if ($sessionToken === '' || ! hash_equals($sessionToken, $submittedToken)) {
            return redirect()->to(site_url('dashboard'))->with('staff_error', 'Your staff form session expired. Please try again.');
        }

        $user = (new UserModel())->find($userId);
        if ($user === null || $user['role'] !== 'employee') {
            return redirect()->to(site_url('dashboard'))->with('staff_error', 'Employee account not found.');
        }

        $status = trim((string) $this->request->getPost('accountStatus'));
        $department = trim((string) $this->request->getPost('department'));
        $newPassword = (string) $this->request->getPost('newPassword');

        if (! in_array($status, ['active', 'pending', 'inactive'], true) || ! in_array($department, self::DEPARTMENTS, true)) {
            return redirect()->to(site_url('dashboard'))->with('staff_error', 'Select a valid status and department.');
        }

        if ($newPassword !== '' && (strlen($newPassword) < 8 || strlen($newPassword) > 255)) {
            return redirect()->to(site_url('dashboard'))->with('staff_error', 'A new password must contain at least 8 characters.');
        }

        $accountUpdate = ['account_status' => $status];
        if ($newPassword !== '') {
            $accountUpdate['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        try {
            (new UserModel())->update($userId, $accountUpdate);
            (new EmployeeModel())->where('user_id', $userId)->set('department', $department)->update();
        } catch (Throwable $exception) {
            log_message('error', 'Staff account update failed: {message}', ['message' => $exception->getMessage()]);

            return redirect()->to(site_url('dashboard'))->with('staff_error', 'The employee account could not be updated. Please try again.');
        }

        $session->remove('staff_csrf_token');

        return redirect()->to(site_url('dashboard'))->with('staff_success', 'Employee access was updated.');
    }

    private function cleanInput(): array
    {
        $text = fn (string $key): string => trim((string) $this->request->getPost($key));

        return [
            'firstName' => $text('firstName'),
            'middleName' => $text('middleName'),
            'lastName' => $text('lastName'),
            'birthdate' => $text('birthdate'),
            'gender' => $text('gender'),
            'department' => $text('department'),
            'email' => strtolower($text('email')),
            'phone' => $text('phone'),
            'address' => $text('address'),
            'username' => $text('username'),
            'password' => (string) $this->request->getPost('password'),
            'accountStatus' => $text('accountStatus'),
        ];
    }

    private function validateInput(array $data): array
    {
        $errors = [];
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $data['birthdate']);

        if ($data['firstName'] === '' || strlen($data['firstName']) > 100 || $data['lastName'] === '' || strlen($data['lastName']) > 100) {
            $errors[] = 'Enter a valid first and last name.';
        }
        if (strlen($data['middleName']) > 100) {
            $errors[] = 'Middle name must be 100 characters or fewer.';
        }
        if (! $date || $date->format('Y-m-d') !== $data['birthdate'] || $date > new DateTimeImmutable('today')) {
            $errors[] = 'Enter a valid birthdate.';
        }
        if (! in_array($data['gender'], ['Female', 'Male', 'Other'], true)) {
            $errors[] = 'Select a valid gender.';
        }
        if (! in_array($data['department'], self::DEPARTMENTS, true)) {
            $errors[] = 'Select a valid department.';
        }
        if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 254) {
            $errors[] = 'Enter a valid email address.';
        }
        if (! preg_match('/^[0-9+()\s-]{7,32}$/', $data['phone'])) {
            $errors[] = 'Enter a valid phone number.';
        }
        if ($data['address'] === '' || strlen($data['address']) > 500) {
            $errors[] = 'Enter an address up to 500 characters.';
        }
        if (! preg_match('/^[A-Za-z0-9._-]{3,50}$/', $data['username'])) {
            $errors[] = 'Use a valid username with 3 to 50 characters.';
        }
        if (strlen($data['password']) < 8 || strlen($data['password']) > 255) {
            $errors[] = 'Password must contain at least 8 characters.';
        }
        if (! in_array($data['accountStatus'], ['active', 'pending', 'inactive'], true)) {
            $errors[] = 'Select a valid account status.';
        }

        return $errors;
    }
}
