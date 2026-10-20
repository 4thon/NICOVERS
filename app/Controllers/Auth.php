<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        $session = session();

        if ($session->get('auth_user_id') !== null) {
            return redirect()->to(site_url('dashboard'));
        }

        $token = bin2hex(random_bytes(32));
        $session->set('login_csrf_token', $token);

        return view('login', ['csrfToken' => $token]);
    }

    public function attempt()
    {
        $session = session();
        $submittedToken = (string) $this->request->getPost('csrfToken');
        $sessionToken = (string) $session->get('login_csrf_token');

        if ($sessionToken === '' || ! hash_equals($sessionToken, $submittedToken)) {
            return redirect()->to(site_url('login'))->with('login_error', 'Your session expired. Please try again.');
        }

        $identifier = trim((string) $this->request->getPost('identifier'));
        $password = (string) $this->request->getPost('password');

        if ($identifier === '' || $password === '') {
            return redirect()->to(site_url('login'))->withInput()->with('login_error', 'Enter your email or username and password.');
        }

        $user = (new UserModel())
            ->groupStart()
            ->where('username', $identifier)
            ->orWhere('email', strtolower($identifier))
            ->groupEnd()
            ->first();

        if ($user === null || ! password_verify($password, $user['password_hash'])) {
            return redirect()->to(site_url('login'))->withInput()->with('login_error', 'The sign-in details do not match our records.');
        }

        if ($user['account_status'] !== 'active') {
            $message = $user['account_status'] === 'pending'
                ? 'This staff account is waiting for HR or administrator activation.'
                : 'This account is inactive. Please contact Sun Son Solar support.';

            return redirect()->to(site_url('login'))->withInput()->with('login_error', $message);
        }

        if (! in_array($user['role'], ['customer', 'employee', 'admin'], true)) {
            return redirect()->to(site_url('login'))->with('login_error', 'This account does not have portal access.');
        }

        $session->regenerate(true);
        $session->set([
            'auth_user_id' => (int) $user['id'],
            'auth_role' => $user['role'],
            'auth_name' => $this->displayName((int) $user['id'], $user['role'], $user['username']),
            'logout_csrf_token' => bin2hex(random_bytes(32)),
        ]);
        $session->remove('login_csrf_token');

        return redirect()->to(site_url('dashboard'));
    }

    public function logout()
    {
        $session = session();
        $submittedToken = (string) $this->request->getPost('csrfToken');
        $sessionToken = (string) $session->get('logout_csrf_token');

        if ($sessionToken !== '' && hash_equals($sessionToken, $submittedToken)) {
            $session->destroy();
        }

        return redirect()->to(site_url('login'));
    }

    private function displayName(int $userId, string $role, string $fallback): string
    {
        if ($role === 'admin') {
            return $fallback;
        }

        $table = $role === 'employee' ? 'employees' : 'customers';
        $profile = db_connect()->table($table)
            ->select('first_name, last_name')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if ($profile === null) {
            return $fallback;
        }

        return trim($profile['first_name'] . ' ' . $profile['last_name']);
    }
}
