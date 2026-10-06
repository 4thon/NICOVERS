# Sun Son Solar Registration System

## Overview

This project implements a CodeIgniter 4 web-based registration form for Sun Son Solar. Users can register as either a customer or an employee. The form performs client-side validation before submission, then forwards the data to a CodeIgniter controller that re-validates everything server-side before writing to a MySQL database.

Customer accounts are created with an `active` status immediately. Employee accounts go into a `pending` state because they require departmental assignment and administrative approval.

## Project Structure

```text
.
|-- app/
|   |-- Controllers/Register.php
|   |-- Database/Migrations/
|   |-- Database/Schema/schema.sql
|   |-- Models/
|   `-- Views/register.php
|-- public/
|   |-- assets/css/register.css
|   |-- assets/js/register.js
|   `-- index.php
|-- system/                   CodeIgniter framework files
|-- writable/                 Runtime cache, logs, and sessions
|-- _legacy_standalone/       Previous non-CodeIgniter implementation
|-- .env                      Local CodeIgniter environment settings
|-- composer.json
|-- spark
|-- .gitignore
`-- README.md
```

The active entry point is now CodeIgniter's `public/index.php`. In XAMPP, open `http://localhost/Regis/public/` or `http://localhost/Regis/public/register`.

## Registration Form

The form is split into four sections that appear in sequence:

1. **Personal Information** — name fields, birthdate, and gender.
2. **Employment Details** — department dropdown, shown only when the Employee role is selected.
3. **Contact and Location** — email, phone, and a free-text address.
4. **Account Credentials** — username and password with confirmation.

A role toggle in the page header switches between Customer and Employee. The Employment Details section and the section numbering adjust dynamically based on which role is active.

### Fields

| Field | Input type | Required | Validation rules |
|---|---|---|---|
| First name | text | yes | non-empty, max 100 chars |
| Middle name | text | no | max 100 chars |
| Last name | text | yes | non-empty, max 100 chars |
| Birthdate | date | yes | valid past date in `YYYY-MM-DD` format |
| Gender | select | yes | one of Female, Male, Other |
| Department | select | yes (employees only) | one of Installation, Maintenance and Repair, System Design, Sales and Consultation, Administration |
| Email address | email | yes | valid email format, max 254 chars, lowercased on the server |
| Phone number | tel | yes | digits, `+`, `-`, spaces, parentheses; minimum 7 characters |
| Address | textarea | yes | non-empty, max 500 characters |
| Username | text | yes | 3–50 chars, letters, digits, dots, underscores, hyphens only (`^[A-Za-z0-9._-]{3,50}$`) |
| Password | password | yes | minimum 8 characters, max 255 characters on the server |
| Confirm password | password | yes | must match the password field exactly |

The password fields include a toggle button (eye icon) that switches the input between `password` and `text` type so the user can verify what they typed. Each field has a dedicated error message element that becomes visible only when the field is flagged as invalid.

## Client-Side Validation Logic

The JavaScript in `assets/js/register.js` performs two layers of validation:

**Live validation.** Every input field listens for `blur` events and runs the validation pass immediately, adding the `invalid` class to the field and activating its error message. This gives the user feedback as soon as they move away from a field.

**Submit validation.** When the form is submitted, the full `validate()` function runs. It checks all required fields, tests the email and username against regular expressions, enforces the password length and match checks, and returns a boolean. If any field is invalid, the first invalid field receives focus and the form does not proceed.

The validation function checks the following conditions:

- `firstName` and `lastName` must be non-empty.
- `middleName` may be left blank.
- `birthdate` must be non-empty and parse as a real date.
- `gender` must be selected.
- `department` is required only when the role is `employee`.
- `email` must match the pattern `^[^\s@]+@[^\s@]+\.[^\s@]+$`.
- `phone` must match the pattern `/^[0-9+\-\s()]{7,}$/`.
- `address` must be non-empty.
- `username` must match `/^[A-Za-z0-9._-]{3,50}$/`.
- `password` must be at least 8 characters.
- `confirmPassword` must be non-empty and identical to `password`.

## Registration Flow

1. The user fills out the form and clicks **Create account**.
2. The browser runs `validate(true)`. If any field fails, the error message appears inline and the submission stops.
3. On success, the browser sends a `GET` request to `api/register.php?action=csrf`. The server generates a 32-byte random token, stores it in the PHP session, and returns it as JSON.
4. The browser then sends a `POST` request to `api/register.php` with the form data as a JSON body and the CSRF token in the `X-CSRF-Token` header.
5. The server compares the submitted token against the session value using `hash_equals` (a timing-safe comparison). A mismatch returns `403`.
6. The server re-runs all validation rules. If any fail, it responds with `422` and a map of field names to error messages.
7. On success, the server starts a database transaction, inserts the user record with a hashed password (`password_hash` with `PASSWORD_DEFAULT`), and inserts the matching customer or employee profile row. The transaction commits.
8. The CSRF token is cleared from the session so it cannot be reused.
9. The browser displays the success message and resets the form.

## CSRF Protection

Every registration submission requires a token issued by the same session. The server stores the token in `$_SESSION['csrf_token']` and uses `SameSite=Strict` plus `HttpOnly` cookie flags. The token is single-use — it is deleted from the session after a successful or failed submission attempt, forcing the client to request a fresh token on the next attempt.

## Database

The schema (`database/schema.sql`) creates three tables:

- **`users`** — a shared account table holding email, username, password hash, role, account status, and a creation timestamp. Email and username each have a unique constraint.
- **`customers`** — personal and contact details linked to `users` via a foreign key.
- **`employees`** — personal and contact details plus a department column, also linked to `users`.

The employee table's `department` column is an `ENUM` that mirrors the five options in the dropdown. A foreign key with `ON DELETE CASCADE` ensures profile rows are removed when the parent user row is deleted.

The schema also seeds two demo accounts — one customer and one employee — so the database is immediately usable for local development.

Email addresses and usernames are unique. Passwords are never stored in plaintext; only the `password_hash` column from `password_hash($password, PASSWORD_DEFAULT)` is persisted.

## Local Setup

1. Install PHP with the `pdo_mysql` extension and run a local MySQL or MariaDB instance.
2. Import the schema:

   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. Set the environment variables if your database differs from the defaults:

   ```text
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=sun_son_solar
   DB_USER=root
   DB_PASSWORD=
   ```

4. Start the built-in PHP server from the project root:

   ```bash
   php -S 127.0.0.1:8000
   ```

5. Visit `http://127.0.0.1:8000/register.html`.

Opening `register.html` via a `file://` URL will not work. The registration flow depends on the PHP session and its cookie, which require a local server.

## Error Handling

The PHP backend returns errors in two ways:

- **Validation errors (422):** The response body contains a JSON object with a `message` and an `errors` map. Each key in `errors` corresponds to a form field ID, allowing the frontend to mark that specific field as invalid.
- **Conflict errors (409):** If the email or username already exists, the server inspects the PDO exception code. It checks whether the constraint name contains `uq_users_email` to determine the field, then returns the appropriate message.
- **Server errors (500):** Database failures are caught, the transaction is rolled back, and the error is written to the PHP error log. The client sees a generic "temporarily unavailable" message.

The frontend handles all of these by mapping the `errors` keys back to form fields and displaying a summary in the success/error banner below the submit button.
