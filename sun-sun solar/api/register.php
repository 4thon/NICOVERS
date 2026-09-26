<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function respond(int $status, array $payload): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function input(): array
{
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}

function text_value(array $data, string $key): string
{
    return trim((string)($data[$key] ?? ''));
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $secure,
        'samesite' => 'Strict',
        'path' => '/',
    ]);
    session_start();
}

start_secure_session();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'csrf') {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    respond(200, ['csrfToken' => $_SESSION['csrf_token']]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: GET, POST');
    respond(405, ['message' => 'Method not allowed.']);
}

$data = input();
$submittedToken = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
    respond(403, ['message' => 'Your session expired. Refresh the page and try again.']);
}

$allowedDepartments = [
    'Installation', 'Maintenance and Repair', 'System Design',
    'Sales and Consultation', 'Administration',
];
$firstName = text_value($data, 'firstName');
$middleName = text_value($data, 'middleName');
$lastName = text_value($data, 'lastName');
$birthdate = text_value($data, 'birthdate');
$gender = text_value($data, 'gender');
$role = text_value($data, 'role');
$department = text_value($data, 'department');
$email = strtolower(text_value($data, 'email'));
$phone = text_value($data, 'phone');
$address = text_value($data, 'address');
$username = text_value($data, 'username');
$password = (string)($data['password'] ?? '');

$errors = [];
if ($firstName === '' || strlen($firstName) > 100) $errors['firstName'] = 'Enter a first name up to 100 characters.';
if (strlen($middleName) > 100) $errors['middleName'] = 'Middle name must be 100 characters or fewer.';
if ($lastName === '' || strlen($lastName) > 100) $errors['lastName'] = 'Enter a last name up to 100 characters.';
$date = DateTimeImmutable::createFromFormat('!Y-m-d', $birthdate);
if (!$date || $date->format('Y-m-d') !== $birthdate || $date > new DateTimeImmutable('today')) $errors['birthdate'] = 'Enter a valid birthdate.';
if (!in_array($gender, ['Female', 'Male', 'Other'], true)) $errors['gender'] = 'Select a valid gender.';
if (!in_array($role, ['customer', 'employee'], true)) $errors['role'] = 'Select a valid account type.';
if ($role === 'employee' && !in_array($department, $allowedDepartments, true)) $errors['department'] = 'Select a valid department.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) $errors['email'] = 'Enter a valid email address.';
if (!preg_match('/^[0-9+()\s-]{7,32}$/', $phone)) $errors['phone'] = 'Enter a valid phone number.';
if ($address === '' || strlen($address) > 500) $errors['address'] = 'Enter an address up to 500 characters.';
if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) $errors['username'] = 'Use 3-50 letters, numbers, dots, underscores or hyphens.';
if (strlen($password) < 8 || strlen($password) > 255) $errors['password'] = 'Password must be at least 8 characters.';

if ($errors) {
    respond(422, ['message' => 'Please correct the highlighted fields.', 'errors' => $errors]);
}

try {
    $connection = database();
    $connection->beginTransaction();

    $accountStatement = $connection->prepare(
        'INSERT INTO users (email, username, password_hash, role, account_status)
         VALUES (:email, :username, :password_hash, :role, :account_status)'
    );
    $accountStatement->execute([
        'email' => $email,
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
        'account_status' => $role === 'employee' ? 'pending' : 'active',
    ]);

    $profileSql = $role === 'employee'
        ? 'INSERT INTO employees (user_id, first_name, middle_name, last_name, birthdate, gender, department, phone, address)
           VALUES (:user_id, :first_name, :middle_name, :last_name, :birthdate, :gender, :department, :phone, :address)'
        : 'INSERT INTO customers (user_id, first_name, middle_name, last_name, birthdate, gender, phone, address)
           VALUES (:user_id, :first_name, :middle_name, :last_name, :birthdate, :gender, :phone, :address)';
    $profileStatement = $connection->prepare($profileSql);
    $profileData = [
        'user_id' => $connection->lastInsertId(),
        'first_name' => $firstName,
        'middle_name' => $middleName !== '' ? $middleName : null,
        'last_name' => $lastName,
        'birthdate' => $birthdate,
        'gender' => $gender,
        'phone' => $phone,
        'address' => $address,
    ];
    if ($role === 'employee') {
        $profileData['department'] = $department;
    }
    $profileStatement->execute($profileData);
    $connection->commit();

    unset($_SESSION['csrf_token']);
    $message = $role === 'employee'
        ? 'Your employee registration was submitted for approval.'
        : 'Your account has been created. You can now sign in.';
    respond(201, ['message' => $message]);
} catch (PDOException $exception) {
    if (isset($connection) && $connection->inTransaction()) {
        $connection->rollBack();
    }
    if ($exception->getCode() === '23000') {
        $isEmail = str_contains(strtolower($exception->getMessage()), 'uq_users_email');
        $field = $isEmail ? 'email' : 'username';
        $message = $isEmail ? 'That email address is already registered.' : 'That username is already taken.';
        respond(409, ['message' => $message, 'errors' => [$field => $message]]);
    }
    error_log('Registration database error: ' . $exception->getMessage());
    respond(500, ['message' => 'Registration is temporarily unavailable. Please try again later.']);
}
