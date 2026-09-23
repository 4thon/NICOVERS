# Sun Son Solar registration

## Backend layout

- `api/register.php` provides the CSRF token and accepts registration requests.
- `config/database.php` creates the PDO/MySQL connection from environment variables.
- `database/schema.sql` creates the database and `users` table.
- `assets/js/register.js` validates the form and posts to the registration API.

The reference ZIP supplied a MySQL connection and a `registration` table example. The implementation keeps its personal/contact data fields, adds account role and employee department, and uses prepared queries plus PHP password hashing. Customer accounts are active after registration; employee accounts are marked pending for approval. No credentials from the reference are required.

## Run locally

1. Install PHP with `pdo_mysql` enabled and MySQL/MariaDB.
2. Import `database/schema.sql` into MySQL.
3. Set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` for the PHP process. Defaults are `127.0.0.1:3306`, database `sun_son_solar`, user `root`, and an empty password for local development.
4. From this directory, run `php -S 127.0.0.1:8000` and open `http://127.0.0.1:8000/register.html`.

Do not open `register.html` directly with a `file://` URL; the form needs PHP to serve the API and its session cookie. Use a real secret database password outside local development.
