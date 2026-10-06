# How to Run the Sun Son Solar Registration Project (Step-by-Step)

> I'm in **Explore Mode** (read-only investigation) — I can't start servers or run commands for you. Below is the exact guide. To have it executed for you, switch to **Act Mode** using the mode selector at the bottom of the chat. Your findings carry over.

**What this project is (one line):** A CodeIgniter 4 (PHP 8.2+) web app with a single registration form. It saves new users into MySQL as a *Customer* (active) or *Employee* (pending). It lives at `e:\XAMPP\htdocs\Regis`.

Saved as `project_info__1.md` in the project root.

---

## Part 1 — Before you start (one-time setup)

### Step 1. Open XAMPP Control Panel
Start **Apache** and **MySQL**. Both should turn green.

### Step 2. Check your PHP version
Open Command Prompt and run:

```cmd
E:\XAMPP\php\php.exe -v
```

You need **PHP 8.2 or higher**. If it says 8.1 or lower, this project will not run — upgrade XAMPP's PHP first.

### Step 3. Make sure two PHP extensions are on
Open `E:\XAMPP\php\php.ini` in a text editor and confirm these lines have **no `;` in front of them**:

```ini
extension=intl
extension=mbstring
```

If you changed anything, restart Apache. Without these two, the app refuses to boot with a 503 error.

### Step 4. Create the database
In XAMPP Control Panel, click **MySQL → Admin**. phpMyAdmin opens in your browser.

1. Click the **Import** tab.
2. Click **Choose File** and select:
   `E:\XAMPP\htdocs\Regis\app\Database\Schema\schema.sql`
3. Scroll down and click **Go**.

This creates the `sun_son_solar` database with three tables (`users`, `customers`, `employees`) and two demo accounts.

**Alternative (command line):**

```cmd
E:\XAMPP\mysql\bin\mysql.exe -u root < E:\XAMPP\htdocs\Regis\app\Database\Schema\schema.sql
```

### Step 5. Check the `.env` file
Open `E:\XAMPP\htdocs\Regis\.env`. These lines are already correct for a default XAMPP install:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/Regis/public/'
database.default.hostname = 127.0.0.1
database.default.database = sun_son_solar
database.default.username = root
database.default.password = ''
database.default.port = 3306
```

- **Only change the password line** if your MySQL root user has a password: `database.default.password = 'yourpassword'`
- Do **not** remove the quotes around the empty password.
- Leave `app.baseURL` exactly as it is — the form's links and buttons depend on it.

### Step 6. Install Composer dependencies (only if the page errors out)
If you see a *"Class not found"* or *"Laminas"* error later, open Command Prompt and run:

```cmd
cd /d E:\XAMPP\htdocs\Regis
composer install
```

If `composer` isn't recognized, skip this for now — the app usually boots without it.

---

## Part 2 — Open and run the project

### Step 7. Open your browser
Go to this exact address:

```
http://localhost/Regis/public/register
```

That's it — the registration form should appear.

**Important — the address matters:**

| URL | Result |
|---|---|
| `http://localhost/Regis/public/register` | ✅ Works — this is the correct one |
| `http://localhost/Regis/public/` | ✅ Works — same page |
| `http://localhost/Regis/` | ❌ Blocked / 404 |
| `http://localhost/Regis/app/` | ❌ Blocked by `.htaccess` |

The `/public/` part is the web root. Never open the project folder itself.

### Step 8. Try the form
1. Pick **Customer** or **Employee** in the top-right toggle (picking Employee reveals a Department dropdown).
2. Fill in all fields marked `*`.
3. Password must be **at least 8 characters**, and both password boxes must match.
4. Username: **3–50 characters**, letters/numbers/dots/underscores/hyphens only.
5. Click **Create account**.
6. You should see a green success message — *"Your account has been created..."* for a customer, or *"submitted for approval"* for an employee.

### Step 9. Confirm it actually saved
Back in phpMyAdmin, click the `sun_son_solar` database in the left sidebar, then open the **users** table. Your new account should be listed with:
- `role` = `customer` or `employee`
- `account_status` = `active` (customer) or `pending` (employee)
- a long `$2y$...` password hash

Your details will also be in the **customers** or **employees** table.

---

## Part 3 — If something goes wrong

| What you see | What to do |
|---|---|
| "Your PHP version must be 8.2 or higher" | Upgrade PHP in XAMPP (Step 2) |
| "The framework needs the following extension(s): intl, mbstring" | Enable them in `php.ini` and restart Apache (Step 3) |
| 404 on the register page | Apache isn't running, or `mod_rewrite` is off. Open `httpd.conf` and make sure `LoadModule rewrite_module` is uncommented and `AllowOverride All` is set for `htdocs`. Restart Apache |
| 403 "Your session expired" when submitting | Refresh the page and try again. Also confirm `E:\XAMPP\htdocs\Regis\writable\session` exists and is writable |
| 500 error on submit | The database isn't reachable or doesn't exist. Re-check Steps 4 and 5 |
| Blank white page | Open `E:\XAMPP\htdocs\Regis\writable\logs\` and read the newest `log-*.log` file — the real error is written there |
| "That email address is already registered" | That's expected. Use a different email/username |

---

## Part 4 — Optional: run without Apache

If you'd rather use PHP's built-in server:

1. Change `app.baseURL` in `.env` to `app.baseURL = 'http://localhost:8080/'`
2. Run:

```cmd
cd /d E:\XAMPP\htdocs\Regis
php spark serve
```

3. Open `http://localhost:8080/register`

Using **Apache instead is simpler and recommended**, because `.env` is already set up for it.

---

## Quick recap (the whole thing in 5 lines)

1. Start **Apache + MySQL** in XAMPP.
2. Import `app\Database\Schema\schema.sql` into phpMyAdmin.
3. Confirm `.env` has database `sun_son_solar`, user `root`, empty password.
4. Open `http://localhost/Regis/public/register`.
5. Fill the form and submit — check the `users` table in phpMyAdmin to confirm.

---

*Want me to dig into how the code works (validation rules, CSRF flow, database design)? Just ask.*