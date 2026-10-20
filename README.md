# Sun Son Solar Registration System

DEVELOPED BY: NICOVERS

Sarmiento Antonio Miguel - Frontend 
Pascual, Kyle Jimfred - Backend
Pacheco, Janah - Documentation
Caber, Joseph Benedict - Data Analyst


## Overview

This project is a CodeIgniter 4 foundation for the Sun Son Solar customer portal and operations system. The public homepage introduces the company, solar benefits, services, customer stories, FAQs, contact details, and a secure quote-request flow.

Only customers can create an account through the public registration page. Employees and administrators are created and managed by HR or administrators, preventing public employee self-registration. Customer accounts are created with an `active` status; employee accounts can be assigned a `pending`, `active`, or `inactive` status by an authorized workflow.

## Project Structure

```text
.
|-- app/
|   |-- Controllers/Auth.php
|   |-- Controllers/Dashboard.php
|   |-- Controllers/Home.php
|   |-- Controllers/Products.php
|   |-- Controllers/Quote.php
|   |-- Controllers/Register.php
|   |-- Controllers/StaffAccounts.php
|   |-- Database/Migrations/
|   |-- Database/Schema/schema.sql
|   |-- Models/
|   |-- Views/home.php
|   |-- Views/login.php
|   |-- Views/products/
|   `-- Views/register.php
|-- public/
|   |-- assets/css/home.css
|   |-- assets/css/login.css
|   |-- assets/css/portal.css
|   |-- assets/css/products.css
|   |-- assets/css/register.css
|   |-- assets/js/home.js
|   |-- assets/js/product3d.js
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

The active entry point is now CodeIgniter's `public/index.php`. In XAMPP, open `http://localhost/Regis/public/` for the homepage or `http://localhost/Regis/public/register` for customer registration.

## Product Catalogue

The public catalogue is available at `http://localhost/Regis/public/products`. Visitors can search products, filter by category, open an individual product page, inspect its system information, and open a focused product inquiry form.

The catalogue contains the confirmed product groups from the client interview: solar panels, inverters, batteries, racking and mounting systems, and solar wires. Product prices and supplier-specific warranty values are intentionally not invented; they remain empty until an authorized administrator enters the final data.

The catalogue has 15 approved planning examples across the five product groups. They are examples for discovery and consultation, not invented supplier inventory, prices, or warranty claims.

Each product detail page uses a procedural Three.js viewer for a rotating and zoomable solar component. Selecting **Inquire about this product** opens an animated, contained dialog with the 3D panel on the left and a validated inquiry form on the right. It uses Three.js and OrbitControls from the jsDelivr CDN, so it does not require a Node.js package install or a downloaded 3D model. The `products.model_url` field remains available for a future approved `.glb` model.

## Inquiries And Department Workspaces

- Product inquiries are saved in `product_inquiries`, initially routed to **Marketing Sales**. Customers can see their submitted inquiry and its current status from their portal.
- Marketing Sales and administrators can update an inquiry from `new` through `reviewing`, `quoted`, or `closed`. Other departments cannot change it.
- Employee dashboards show a live department summary, only their own assigned service jobs, a location-aware check-in form, and a server-validated service-report form. Both forms reject a service request that is not assigned to the signed-in employee.
- Employees and administrators can send internal department messages through the portal. The active-account panel uses the latest authenticated portal activity and marks accounts seen within ten minutes as active.
- Administrator analytics are live counts from the database for product inquiries, employees, and support tickets.

## Login and Roles

Open `http://localhost/Regis/public/login` to sign in. The login page accepts either an email address or username with a password. It does **not** ask the person to select a role.

After the password is verified, the server reads the account's `users.role` value and starts the correct session:

- **Customer** accounts are created only through the public registration form and open the customer portal.
- **Employee** accounts are created by an authorized administrator through the employee-account form in the administrator portal. An employee can sign in only when their `account_status` is `active`.
- **Admin** accounts open the operations portal, where authorized staff can create employee accounts and choose their status: `active`, `pending`, or `inactive`.

This keeps role assignment on the server and prevents someone from claiming an employee or administrator role through the browser. The only sample login is the explicitly local-only test administrator described below; it is created only when its seed command is run.

For the first administrator only, use phpMyAdmin to change the real, authorized owner's existing account in `users` to `role = admin` and `account_status = active`. The account keeps its existing password. After this one-time setup, that administrator can activate employee accounts, set their passwords, and create future employee accounts inside the portal.

## Registration Form

The public customer registration form is split into three sections that appear in sequence:

1. **Personal Information** — name fields, birthdate, and gender.
2. **Contact and Location** — email, phone, and a free-text address.
3. **Account Credentials** — username and password with confirmation.

Employee department selection is not exposed publicly. Employee profiles are created by HR or administrators using the approved department list: Administration, IT, Despatch, Accounting, HR, Marketing Sales, and Customer Service.

### Fields

| Field | Input type | Required | Validation rules |
|---|---|---|---|
| First name | text | yes | non-empty, max 100 chars |
| Middle name | text | no | max 100 chars |
| Last name | text | yes | non-empty, max 100 chars |
| Birthdate | date | yes | valid past date in `YYYY-MM-DD` format |
| Gender | select | yes | one of Female, Male, Other |
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
- `email` must match the pattern `^[^\s@]+@[^\s@]+\.[^\s@]+$`.
- `phone` must match the pattern `/^[0-9+\-\s()]{7,}$/`.
- `address` must be non-empty.
- `username` must match `/^[A-Za-z0-9._-]{3,50}$/`.
- `password` must be at least 8 characters.
- `confirmPassword` must be non-empty and identical to `password`.

## Registration Flow

1. The user fills out the form and clicks **Create account**.
2. The browser runs `validate(true)`. If any field fails, the error message appears inline and the submission stops.
3. On success, the browser sends a `GET` request to `/register/csrf`. The controller generates a 32-byte random token, stores it in the CodeIgniter session, and returns it as JSON.
4. The browser then sends a `POST` request to `/register` with the form data as a JSON body and the CSRF token in the `X-CSRF-Token` header.
5. The server compares the submitted token against the session value using `hash_equals` (a timing-safe comparison). A mismatch returns `403`.
6. The server re-runs all validation rules. If any fail, it responds with `422` and a map of field names to error messages.
7. On success, the server starts a database transaction, inserts a customer account with a hashed password (`password_hash` with `PASSWORD_DEFAULT`), and inserts the linked customer profile row. The transaction commits.
8. After a successful registration, the CSRF token is cleared from the session so it cannot be reused.
9. The browser displays the success message and resets the form.

## CSRF Protection

Every registration submission requires a token issued by the same session. The server stores the token in `$_SESSION['csrf_token']` and uses `SameSite=Strict` plus `HttpOnly` cookie flags. The token is single-use — it is deleted from the session after a successful or failed submission attempt, forcing the client to request a fresh token on the next attempt.

## System Data Model

The registration schema (`app/Database/Schema/schema.sql`) and CodeIgniter migrations create the following groups of tables:

- **`users`** — a shared account table holding email, username, password hash, role, account status, and a creation timestamp. Email and username each have a unique constraint.
- **`customers`** — personal and contact details linked to `users` via a foreign key.
- **`employees`** — personal and contact details plus a department column, also linked to `users`.

The employee table's `department` column is an `ENUM` that mirrors the seven options in the dropdown: Administration, IT, Despatch, Accounting, HR, Marketing Sales, and Customer Service. A foreign key with `ON DELETE CASCADE` ensures profile rows are removed when the parent user row is deleted.

The operations migration adds `service_categories` and `service_requests` for consultation, installation, and maintenance scheduling; notifications and preferences; support tickets; invoices and payments; technician check-ins and reports; plus configurable system settings. The public quote modal writes validated requests to `service_requests` with a `pending` status. Product inquiries, internal department messages, and current account presence are stored in `product_inquiries`, `department_messages`, and `user_presence`.

The product catalogue migration adds `product_categories`, `products`, `product_images`, and `product_specs`. It also prepares `saved_calculations` for the solar savings calculator and `monitoring_readings` for future customer system-monitoring charts.

Email addresses and usernames are unique. Passwords are never stored in plaintext; only the `password_hash` column from `password_hash($password, PASSWORD_DEFAULT)` is persisted.

## Local Setup

1. Start Apache and MySQL from the XAMPP Control Panel.
2. Create a `.env` file with the local database settings for `sun_son_solar`.
3. Run the CodeIgniter migrations from the project root:

   ```bash
   php spark migrate
   ```

4. Load the approved catalogue categories and product groups:

   ```bash
   php spark db:seed ProductCatalogueSeeder
   ```

5. Establish one real active administrator account through your approved administrator onboarding process. The administrator can then create and activate employee accounts from the portal.
6. Open `http://localhost/Regis/public/`.

### Local Test Administrator

For local development only, create or reset the test administrator with:

```bash
php spark db:seed TestAdminSeeder
```

Sign in at `http://localhost/Regis/public/login` with username `admin` and password `admin123`. This account is active and has the `admin` role. Remove it or replace its password before deploying the system.

Do not open the view file directly through a `file://` URL. The registration flow requires CodeIgniter, the database connection, and the session cookie.

## Error Handling

The PHP backend returns errors in two ways:

- **Validation errors (422):** The response body contains a JSON object with a `message` and an `errors` map. Each key in `errors` corresponds to a form field ID, allowing the frontend to mark that specific field as invalid.
- **Conflict errors (409):** If the email or username already exists, the server inspects the PDO exception code. It checks whether the constraint name contains `uq_users_email` to determine the field, then returns the appropriate message.
- **Server errors (500):** Database failures are caught, the transaction is rolled back, and the error is written to the PHP error log. The client sees a generic "temporarily unavailable" message.

The frontend handles all of these by mapping the `errors` keys back to form fields and displaying a summary in the success/error banner below the submit button.
