# Sun Son Solar Registration System

## Overview

This project provides a web-based registration form for Sun Son Solar customers and employees. Submitted information is validated in the browser and on the server, then stored in a MySQL database.

Customer accounts are created with an `active` status. Employee accounts require a department and are created with a `pending` status for approval.

## Registration Form

The registration form collects the following information:

- Personal information: first name, middle name, last name, birthdate, and gender.
- Employment information: department for employee registrations only.
- Contact information: email address, phone number, and address.
- Account information: username, password, and password confirmation.

Available employee departments are Installation, Maintenance and Repair, System Design, Sales and Consultation, and Administration.

## Project Structure

```text
sun-sun-solar/
|-- api/
|   `-- register.php          Registration and CSRF API
|-- assets/
|   |-- css/register.css      Registration page styles
|   `-- js/register.js        Form interaction and client validation
|-- config/
|   `-- database.php          PDO database connection
|-- database/
|   `-- schema.sql            Database tables and initial sample records
|-- register.html             Registration page
`-- README.md                 Project documentation
```

## Database

The database schema creates three related tables:

- `users` stores common account details: email address, username, password hash, role, account status, and registration timestamp.
- `customers` stores personal and contact information for customer accounts.
- `employees` stores personal and contact information, including department, for employee accounts.

Every registration creates one row in `users` and one matching row in either `customers` or `employees`. The schema also creates one initial customer account and one initial employee account for local development.

Passwords are stored only as hashes. Email addresses and usernames are unique.

## Local Setup

1. Install PHP with the `pdo_mysql` extension enabled and install MySQL or MariaDB.
2. Import `database/schema.sql` into your MySQL server.
3. Configure the following environment variables when your database uses values different from the local defaults:

   ```text
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=sun_son_solar
   DB_USER=root
   DB_PASSWORD=
   ```

4. Start the PHP development server from the project directory:

   ```powershell
   php -S 127.0.0.1:8000
   ```

5. Open `http://127.0.0.1:8000/register.html` in a browser.

Do not open `register.html` through a `file://` URL because registration requires the PHP API and its session cookie.

## Registration Flow

1. The browser validates the form fields before submission.
2. The browser requests a CSRF token from `api/register.php`.
3. The form data and CSRF token are sent to the registration API.
4. The API validates the request, hashes the password, and inserts the account into `users` plus the matching customer or employee table.
5. The form displays either a confirmation message or the relevant validation error.
