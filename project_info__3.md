> **Mode note:** I'm in **Explore Mode** (read-only). Everything below is investigation plus a proposed plan. To implement anything (CSS, landing animation, migrations), switch to **Act Mode** via the mode selector at the bottom of the chat — these findings carry over as context.

Report saved to `project_info__2.md` in the project root.

---

# Sun Son Solar (`Regis`) — Codebase Overview & Redesign Readiness Report

## Summary

This is **not** a marketing website. It is a **CodeIgniter 4 (PHP 8.2+) registration application** for "Sun Son Solar" that renders **one page**: a customer/employee sign-up form served at both `/` and `/register`. It validates client-side (vanilla JS), re-validates server-side, and writes into a MySQL schema of three tables (`users`, `customers`, `employees`).

There is **no landing page, no hero, no public site, no build system, and no image assets**. The requested redesign (landing animation, parallax, `departments` table, employee directory UI) is therefore **net-new surface area built on top of a working registration app**, not a restyle of an existing site. The *Gap Analysis* section below is the most important part of this report — several requirements directly contradict the current schema and routing.

---

## Technology Stack

| Layer | What's actually used | Evidence |
|---|---|---|
| Language / runtime | PHP **^8.2** (enforced at boot) | `composer.json`, `public/index.php` version gate |
| Framework | **CodeIgniter 4** (stock, unmodified) | `system/` is verbatim CI4 |
| Extensions required | `ext-intl`, `ext-mbstring` — hard requirement, app refuses to boot without them | `composer.json`, `system/Boot.php` |
| Database | **MySQLi / MariaDB**, db `sun_son_solar` | `app/Config/Database.php`, `.env` |
| Front-end | **Hand-written CSS + hand-written vanilla JS**. No framework, no bundler, no npm, **no `package.json`** | `public/assets/css/register.css` (~10 KB), `public/assets/js/register.js` (~7 KB) |
| Typeface | **Poppins** via Google Fonts CDN, weights 300–800 | `<link>` in `app/Views/register.php` |
| Templating | CI4 PHP views (no Twig/Blade) | `app/Views/register.php` |
| CSP | `CSPEnabled = false` → **inline styles/scripts and CDN `<script>` tags are unblocked** | `app/Config/App.php` |
| Build tooling | **None.** Assets are edited directly in `public/assets/`. There is nothing to `npm run build`. | — |
| Tests | Default CI4 skeleton only; **no application tests** | `tests/unit/`, `tests/database/` untouched |

**Deployment shape:** XAMPP on Windows; the docroot is the `public/` subfolder. `.env` sets `app.baseURL = 'http://localhost/Regis/public/'`. Every asset URL goes through `base_url()` / `site_url()` — **hardcoding `/assets/...` will break at this subfolder path.**

---

## Directory Structure (annotated, app-relevant only)

```
Regis/                              ← XAMPP htdocs subfolder (NOT the docroot)
├── .env                            ← gitignored; local DB creds + app.baseURL
├── spark                           ← CI4 CLI (migrations, seeds)
├── composer.json                   ← CI4 framework only; no front-end deps
├── project_info__1.md              ← prior explore output (how-to-run guide)
├── .kilo/                          ← agent tool artifact dir (gitignored)
│
├── app/                            ← APPLICATION CODE — this is where you work
│   ├── Config/
│   │   ├── Routes.php              ← only 4 routes; '/' and 'register' both → Register::index
│   │   ├── App.php                 ← baseURL, CSP off, indexPage='index.php'
│   │   ├── Database.php            ← 'default' group + 'tests' SQLite group
│   │   └── Filters.php             ← ⚠ ALL globals commented out, incl. csrf
│   ├── Controllers/
│   │   ├── BaseController.php      ← empty scaffold
│   │   ├── Register.php            ← ⭐ the entire app: index, csrf, create
│   │   └── Home.php                ← ⚠ DEAD CODE — no route points to it
│   ├── Models/
│   │   ├── UserModel.php           ← table 'users'
│   │   ├── CustomerModel.php       ← table 'customers'
│   │   ├── EmployeeModel.php       ← table 'employees'  ← will be rewritten
│   │   └── .gitkeep
│   ├── Views/
│   │   ├── register.php            ← ⭐ the ONLY real view (~250 lines)
│   │   ├── welcome_message.php     ← ⚠ CI4 stock page, unreachable
│   │   └── errors/                 ← CI4 default error views
│   └── Database/
│       ├── Schema/schema.sql       ← ⭐ authoritative DDL + demo seeds
│       ├── Migrations/2026-10-06-220500_CreateRegistrationTables.php
│       └── Seeds/.gitkeep          ← ⚠ NO seeder classes exist despite the folder
│
├── public/                         ← WEB ROOT
│   ├── index.php                   ← front controller
│   ├── .htaccess                   ← rewrite → index.php; ✔ mod_rewrite required
│   └── assets/
│       ├── css/register.css        ← ⭐ the whole design system, 12 CSS variables
│       └── js/register.js          ← ⭐ carousel + role toggle + validation + fetch
│
├── system/                         ← CI4 framework — DO NOT MODIFY
├── writable/                       ← cache / logs / sessions (permissions-sensitive)
└── tests/                          ← CI4 skeleton, no app coverage
```

**README is stale in two ways:** it references a `_legacy_standalone/` folder that **does not exist**, and it documents a department list that does not match the code.

---

## Architecture & Request Lifecycle

**Pattern:** classic CI4 MVC with an **AJAX-over-JSON** boundary instead of a form POST. The single page loads once; all mutation happens via `fetch()` returning JSON.

### Routes (`app/Config/Routes.php`)

```php
$routes->get('/',  'Register::index');            // renders the form
$routes->get('register', 'Register::index');      // same view, second URL
$routes->get('register/csrf',  'Register::csrf'); // issues a session-bound token
$routes->post('register', 'Register::create');    // creates account
```

Auto-routing is not relied upon; `Home` is unreachable.

### Boot path

1. Apache rewrites everything to `public/index.php`.
2. `public/index.php` asserts PHP ≥ 8.2, defines `FCPATH`, loads `app/Config/Paths.php`, then `Boot::bootWeb($paths)`.
3. `Paths` resolves `system/`, `app/`, `writable/`, `tests/`; `$viewDirectory` is `app/Views`.
4. `Config\Database` **switches to the `tests` SQLite group whenever `ENVIRONMENT === 'testing'`** — a safety net so `phpunit` can never write to live data.

### Registration data flow (the core loop)

1. `GET /register` → `Register::index()` → `view('register')`.
2. Page renders nav, hero bar, media carousel, and the 4-section form. Inline `<script>` sets `window.REGISTER_ENDPOINTS`.
3. User toggles **Customer / Employee** → `data-role` drives `currentRole`; the Employment Details section shows/hides and section numbers renumber `02/03` ⇄ `03/04`.
4. On submit: `validate(true)` runs. If it fails, the first invalid field is focused and nothing is sent.
5. `fetch('register/csrf')` → `Register::csrf()` → session-stored `registration_csrf_token` (lazily created, `bin2hex(random_bytes(32))`) returned as `{csrfToken}`.
6. `fetch('register', POST, JSON body, header X-CSRF-Token)` → `Register::create()`.
7. `create()` compares header vs session with **`hash_equals()`** (timing-safe) → `403` on mismatch.
8. `cleanInput()` normalises (trims; **lowercases email**), `validateInput()` re-runs every rule → `422` + `{errors: {fieldId: message}}`.
9. Uniqueness pre-checks on `users.email` and `users.username` → `409` + field-scoped error.
10. `transStart()` → insert `users` (with `password_hash(..., PASSWORD_DEFAULT)`) → insert `customers` **or** `employees` → `transComplete()`; rollback + log + `500` on any `Throwable`.
11. Session token is **deleted** (`$session->remove`) → single-use CSRF.
12. Client shows the message in `#formSuccess` and resets the form.

### Key invariants

- `users.role` ∈ {`customer`, `employee`}; `account_status` = `active` for customers, **`pending` for employees**.
- Every profile row has a **`UNIQUE` `user_id` FK with `ON DELETE CASCADE`** — one profile per user, no orphans.
- Server error keys are **the HTML element IDs verbatim** (`firstName`, `email`, `confirmPassword`) — the client maps them straight back with `document.getElementById(id)`. **Renaming an input ID silently breaks server error surfacing.**
- The registration CSRF token is **session-bound and single-use** — this is why a double-submit or long-idle tab yields `403 "Your session expired"`.
- Passwords are never echoed back; only the hash is stored.

---

## Current Design System

Everything lives in `public/assets/css/register.css` (single file, no layers, no utilities).

### Palette — 12 variables, all light-mode only

```css
--bg:#f6f1ea;        --card:#fffdfb;      --card-2:#f3ece1;
--border:#e6dccd;    --text:#2e2a24;      --muted:#8a7f70;
--orange:#f2760f;    --orange-dark:#d9590a;
--red:#e94e3c;       --error:#c0392b;
```

- **No dark mode.** No `prefers-color-scheme` block exists anywhere.
- **No type scale, no spacing scale.** Sizes are ad-hoc literals (`25px`, `23px`, `19px`, `14.5px`, `12.5px`) — `14.5px` and `12.5px` recur, which is a smell, not a system.
- **Fixed pixel font sizes throughout — zero `clamp()`.**
- The brand gradient `linear-gradient(135deg, var(--orange), var(--red))` is applied to the logo tile, hero icon, section numbers, active role-toggle pill, media card, and the submit button — it is the de-facto brand mark.

### Layout

- `.auth` = `grid-template-columns: minmax(280px,1fr) minmax(380px,560px)`, `gap:60px`, `padding:48px 6vw 90px`.
- `.navbar` and `.hero-bar` are both `position:sticky` (`top:0` and `top:72px`) — two stacked sticky bars.
- `.form-row` = `repeat(auto-fit, minmax(150px,1fr))` — auto-wraps, no per-row breakpoints.
- Breakpoints: **900px** and **600px** only. **No ≥1400px treatment** — on large desktop the form stays capped at 560px while the left column stretches.
- `.media-stack` uses **hard-coded `width:440px; height:420px`** with absolutely-positioned 300×380 cards (300×340 / 220×300 under 600px). This is the most layout-shift-prone element in the app.

### Motion in the current build

- Transitions only: `0.2s` color, `0.25s` underline width, `0.9s ease` card swap, `0.15s` border, `translateY(-1px)` on button hover.
- **Zero `@keyframes`.** No animation library, no `prefers-reduced-motion` guard, no `IntersectionObserver`.
- `register.js` runs `setInterval(..., 3200)` to alternate `.is-front`/`.is-back` on two cards and rewrites their `innerHTML` after a 900 ms delay from a 5-slide array. It **never clears the interval** and never pauses off-screen or on hover.

---

## Accessibility & Quality Audit (measured, not guessed)

Contrast ratios computed from the actual hex values against their actual backgrounds:

| Pair | Where used | Ratio | Verdict |
|---|---|---|---|
| `--muted #8a7f70` on `--bg #f6f1ea` | hero subtitle, media caption, helper text, nav links | **≈3.5 : 1** | ❌ **Fails WCAG AA (4.5:1)** for 14–14.5 px text |
| `--orange #f2760f` on `--card #fffdfb` | section labels (12.5 px), `.helper-link`, eyebrow | **≈2.8 : 1** | ❌ **Fails AA badly** |
| white on `--orange` end of brand gradient | `.primary-btn`, active role pill | **≈2.9 : 1** | ❌ Fails AA for 15 px text |
| white on `--red #e94e3c` end of gradient | same | **≈3.7 : 1** | ❌ Still fails AA |
| `--text #2e2a24` on `--card` | body copy | **≈13 : 1** | ✅ Passes AAA |

**The palette is not currently AA-compliant**, and the gradient brand mark is the worst offender — and it's on the primary CTA.

Other concrete findings:

- **The app rolls its own CSRF and the framework CSRF filter is disabled.** In `app/Config/Filters.php`, `$globals['before']` has `'csrf'` commented out (as do `honeypot` and `invalidchars`). This is deliberate — the token travels as a custom header, and `$data = getJSON(true)` wouldn't satisfy CI's form-based CSRF check. **Do not "fix" this by enabling the filter; it will break registration.**
- **`validate(false)` on blur validates the entire form.** Every `blur` on any of the 11 tracked fields runs the full pass, so tabbing out of the **first** field immediately paints **all** other required fields red. A real UX bug.
- **Phone validation differs client vs server.** Client: `/^[0-9+\-\s()]{7,}$/` (unbounded). Server: `/^[0-9+()\s-]{7,32}$/` (max 32). A 40-character phone passes the browser then gets `422` with a generic message.
- **Client validates `birthdate` as "non-empty" only**; the server additionally requires a real, non-future date (`DateTimeImmutable::createFromFormat('!Y-m-d', ...)`). Two rule sets again.
- **Touch targets under 44 px.** `.toggle-pass` is **32×32 px**; `.media-dots span` 7×7 px; `.nav-links a` has only `padding-bottom:6px`; `.section-number` 26×26. `.role-toggle button` and `.input-field` (46 px) are fine.
- **Focus states are weak.** `.input-field:focus` sets `outline:none` and changes border colour only. Buttons, role toggle, eye toggles, and nav links rely on the browser default or nothing. **No `:focus-visible` anywhere.**
- **Semantics are thin.** No `<header>`/`<footer>`; no `<h1>` outside the form (`.form-title` is the `<h1>`, `.hero-title` is a `<div>`); `<nav>` holds four `href="#"` placeholders whose only behaviour is `e.preventDefault()` plus an `.active` class.
- **Carousel content is injected via `innerHTML`** from HTML-entity strings, so slide text is invisible without JS, and the dots have no labels.
- **Two things done well:** `aria-live="polite"` on `#formSuccess`, and the password toggles correctly flip `aria-label` + `aria-pressed`.
- **No `prefers-reduced-motion` handling** — the 3.2 s carousel and 0.9 s transforms run unconditionally.
- **Google Fonts loads 6 weights** (300–800) for a page that uses at most 4.

**Performance baseline:** the page is fast today (two small local files + one font CSS). That budget is what the landing animation will spend — treat it as scarce.

---

## Gap Analysis — Request vs. Reality

None of these are blockers, but each needs a decision before implementation.

### 1. There is no website to redesign — there is a form

The brief asks for a **hero with a rising sun, parallax, scroll-triggered reveals, and "enable scrolling"**. The current app is a **single non-scrolling form page**. `GET /` and `GET /register` both render the registration form; there is no separate landing surface.

**Consequence:** a landing page must be *created* (`GET /` → new `Landing` controller + `app/Views/landing.php`), with registration **moved** to `/register` so the existing route keeps working. This adds a route — the brief says "keep current routes unless I say otherwise," so it needs explicit approval.

### 2. Brand name mismatch

The brief says **"Sunsun Solar"**. Code, schema, seeds, README, and page titles all say **"Sun Son Solar"** (demo emails use `sunsonsolar.com` / `sunsonsolar.local`). One spelling must win; the animation's text reveal makes it visible.

### 3. The `employees` table cannot satisfy the new spec as-is

| Requested | Current schema | Conflict |
|---|---|---|
| `department_id` FK → `departments.id` | `employees.department ENUM(5 values)`, **no `departments` table** | New table + FK + data migration required |
| Seed depts: Administration, IT, Despatch, Accounting, HR, Marketing Sales, Customer Service | ENUM: Installation, Maintenance and Repair, System Design, Sales and Consultation, Administration | **Only 1 of 7 overlaps.** The 5-value ENUM must be retired |
| `birthdate` optional | `birthdate DATE NOT NULL` | Needs `ALTER ... NULL` |
| `gender` optional | `gender ENUM(...) NOT NULL` | Needs `ALTER ... NULL` |
| `phone` required, `email` unique | `phone VARCHAR(32) NOT NULL` ✔; **no `email` column** | Add `email` + unique index (second source of truth vs `users.email`) |
| — | `user_id BIGINT UNSIGNED NOT NULL` FK → `users.id` | The new spec's employees have **no user account**. Must become `NULL`-able, or the spec must be amended to always create a `users` row |
| — | `address VARCHAR(500) NOT NULL` | Not in the new spec → must become `NULL`-able or placeholder-filled |
| Seed: Katherine Sinagaraw, Sol Sun Solis | Seed: Sample Employee / Sample Customer | Replace or append |

**Also:** `Register::create()` inserts the literal ENUM string `$clean['department']` and validates against a hard-coded `private const DEPARTMENTS` array. Once departments live in a table, **both the const and the insert must change**, or employee registration starts failing.

### 4. No seeder classes exist

`app/Database/Seeds/` contains only `.gitkeep`. Demo rows live in `schema.sql` as raw `INSERT`s (and in no migration). The requested seeds need either a real `Seeder` class or an extension of `schema.sql` — but note `schema.sql` also **creates the database**, so it is not re-runnable in CI4's migration flow.

### 5. Employee directory UI does not exist

"List with search and department filter" implies a new controller (`Employee`), new model methods, new views, new JS — plus a route (e.g. `GET /employees`). There is also **no authentication or session guard anywhere**: nothing checks `role` or `account_status` after registration, and **there is no login page** (the "Sign In" link is `href="#"`). An employee directory would be **public by default**.

### 6. Front-end tooling is absent

GSAP + ScrollTrigger + Lenis cannot be `npm install`ed — **no `package.json`, no bundler.** Options are CDN `<script defer>` (zero setup, unblocked because CSP is off) or vendored copies in `public/assets/vendor/`. Either way the design must not assume a build step.

---

## Proposed Plan (awaiting your approval)

### Phase A — Design system (no visual change until applied)

Add `public/assets/css/tokens.css`, loaded **before** `register.css`, with **light tokens as default and a `prefers-color-scheme: dark` override block** — no JS toggle needed for dark-ready.

```css
:root{
  /* 6 core tokens */
  --sun-navy:      #0B1B2B;  /* deep navy — dark surfaces, headings on light   */
  --sun-ink:       #101619;  /* body text on light            (≈13:1 on cream) */
  --sun-muted:     #5A6472;  /* secondary text               (≈5.6:1 on cream) */
  --sun-amber:     #F5A524;  /* solar accent — GRAPHICS + large text ONLY       */
  --sun-amber-ink: #8A4B00;  /* amber-family text on light — AA-safe            */
  --sun-teal:      #0E7C72;  /* supporting teal, links/success (AA on cream)    */
  /* surfaces */
  --sun-cream:     #FAF7F2;
  --sun-surface:   #FFFFFF;
  --sun-line:      #E7E2D9;
}
```

Rule that ships with it: **amber is never small text on light** (~1.9:1 there). The CTA becomes navy-on-amber or cream-on-navy, not white-on-orange. Every pair clears **4.5:1**.

**Type scale (fluid, `clamp()`), spacing scale `4/8/16/24/32/48/80/120`**, body `line-height:1.7`, `max-width:~68ch` measure. Poppins trimmed to **2–3 weights** (or swapped for a variable font) to protect LCP.

### Phase B — Landing animation (new `GET /`)

New `app/Controllers/Landing.php` + `app/Views/landing.php`; `Register` keeps `/register`.

1. **Sun**: inline SVG, **`transform` + `opacity` only** (`translateY`, `scale`; a stacked radial-gradient glow div instead of `filter: drop-shadow`), painted by CSS/keyframes so it renders without waiting on JS.
2. Glow + rays pulse via a second keyframe layer.
3. **"Sunsun Solar"** wordmark fades in `ease-out` over **~2.5 s**.
4. Hero content then reveals; scrolling unlocks (start `body{overflow:hidden}`, release on animation end).
5. **`prefers-reduced-motion: reduce` short-circuits the whole sequence** — sun at final position, text visible, no parallax, no Lenis.

**Library choice — GSAP 3 + ScrollTrigger + Lenis, via CDN with `defer`.** GSAP's timeline API gives exact sequencing and a reliable `onComplete` for the scroll unlock; ScrollTrigger handles parallax without hand-rolled scroll math; Lenis supplies the smooth scroll most "high-impact" references depend on. Because **`CSPEnabled` is `false`**, CDN tags work with zero config and there's no bundler to fight. Lenis must be **destroyed on touch devices and under reduced-motion** (it hijacks wheel/touch and hurts mobile scroll feel). If the project must stay offline, vendor the same files into `public/assets/vendor/` — same code, no CDN.

**LCP budget:** hero headline is real DOM text (never canvas/SVG text), the sun is SVG+CSS, heavy layers get `loading="lazy"`/`decoding="async"`, and the intro overlay is sized in `%`/`svh` so unmounting it cannot shift layout.

### Phase C — Parallax & responsive hardening

3–4 layer speeds (sky/clouds slowest → sun mid → ground/panel shapes fastest), each layer `position:absolute` inside one `overflow:hidden` container so nothing widens the viewport. Fix audit items: single-field blur validation, shared phone regex, `:focus-visible` rings, 44 px minimum targets (`toggle-pass` → 44×44), `<header>`/`<main>`/`<footer>` landmarks, correct heading order.

### Phase D — Database + forms

```sql
CREATE TABLE departments (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                          name VARCHAR(100) NOT NULL UNIQUE);
-- 7 seeds: Administration, IT, Despatch, Accounting, HR, Marketing Sales, Customer Service

ALTER TABLE employees
  ADD COLUMN department_id BIGINT UNSIGNED NULL AFTER last_name,
  ADD COLUMN email VARCHAR(254) NULL AFTER phone,
  MODIFY birthdate DATE NULL,
  MODIFY gender ENUM('Female','Male','Other') NULL,
  MODIFY user_id BIGINT UNSIGNED NULL,
  MODIFY address VARCHAR(500) NULL,
  ADD UNIQUE KEY uq_employees_email (email),
  ADD CONSTRAINT fk_employees_department FOREIGN KEY (department_id) REFERENCES departments(id);
```

Delivered as a **new CI4 migration** (not by editing the applied one). `employees.department` (ENUM) is **kept temporarily** and dual-written, then dropped in a follow-up once `Register.php` reads from `departments`. Two employees seed with `department_id` resolved by name lookup — `birthdate` NULL for Katherine, `1967-01-08` for Sol.

Then: `DepartmentModel`, `EmployeeModel` with `search()` / `filterByDepartment()`, `Employee` controller + directory view + reused form view + progressive-enhancement JS, and `Register::DEPARTMENTS` replaced by a lookup.

**Your input is required on three unknowns:** (a) the real **company phone number** — the brief redacts it, so it ships as a placeholder constant in one place; (b) whether employees keep a `users` row (affects `user_id` nullability); (c) whether `/` becomes the landing page and `/employees` the directory (both add routes).

---

## Assumptions & Placeholders

1. **`[COMPANY_PHONE]` is unknown.** Ships as one constant (e.g. `App\Config\Company::$phone`) — never duplicated across views, seeder, and JS. Not `09291230983`, per your instruction.
2. **Address is not in the employee spec** → `employees.address` becomes nullable; none seeded for the two named employees.
3. **`employees.email`** duplicates `users.email` for account-holding employees. Unique index added on `employees.email`; `Register`'s pre-check continues to use `users.email`.
4. **`user_id` becomes nullable** on `employees`, assuming directory-only employees exist. Reversible in one `ALTER` if you prefer mandatory accounts.
5. **Katherine Sinagaraw** has `middle_name`, `birthdate`, `gender` NULL — renders as "—" in the directory rather than being hidden.
6. **Sol Sun Solis birthdate is 1967-01-08** (`first: Sol`, `middle: Sun`, `last: Solis`).
7. **"Sunsun Solar"** is treated as the new landing hero's brand string; existing "Sun Son Solar" strings stay until you confirm the spelling.
8. **GSAP/ScrollTrigger/Lenis come from CDN** unless you require offline/vendored assets.
9. **No secrets introduced.** DB credentials stay in the gitignored `.env`; the placeholders above are non-secret configuration.
10. **Nothing existing is deleted.** `Home.php`, `welcome_message.php`, and demo seed rows stay in place (flagged as dead/stale, not removed) so behaviour is preserved.

---

## Module Reference

| File | Purpose |
|---|---|
| `app/Config/Routes.php` | All 4 routes. `'/'` and `'register'` → `Register::index`; `'register/csrf'` → token; `POST 'register'` → create. **The file to change for a landing page.** |
| `app/Controllers/Register.php` | The whole backend: `index`, `csrf`, `create`, plus `cleanInput`, `validateInput`, `DEPARTMENTS` const, `jsonError`/`jsonFieldError`. Uses `transStart`/`transComplete`. |
| `app/Views/register.php` | The only real view. Nav, sticky hero bar, media carousel, 4-section form, inline `window.REGISTER_ENDPOINTS`. |
| `public/assets/css/register.css` | Entire design system: 12 CSS vars, navbar, hero bar, role toggle, media cards, form, 2 breakpoints (900/600). |
| `public/assets/js/register.js` | Carousel `setInterval`, nav active state, role toggle + renumbering, password eye, `validate()`, blur validation, submit `fetch` with CSRF header and error-key mapping. Not IIFE-wrapped — all globals. |
| `app/Models/UserModel.php` | `users` model; `allowedFields` email/username/password_hash/role/account_status; `returnType = 'array'`. |
| `app/Models/EmployeeModel.php` | `employees` model. **Must gain `department_id`/`email` and nullable-aware fields.** |
| `app/Models/CustomerModel.php` | `customers` model; mirrors employee fields minus department. |
| `app/Database/Schema/schema.sql` | Authoritative DDL + demo seeds. Creates the DB, so not migration-safe. |
| `app/Database/Migrations/2026-10-06-220500_CreateRegistrationTables.php` | Applied migration creating `users`/`customers`/`employees` with raw SQL. **Do not edit — add a new migration.** |
| `app/Config/Filters.php` | All globals (including `csrf`) commented out. Intentional — enabling the framework CSRF filter breaks registration. |
| `app/Config/App.php` | `baseURL`, `CSPEnabled = false`, `indexPage = 'index.php'`. |
| `app/Config/Database.php` | `default` (MySQLi) + `tests` (SQLite `:memory:`); auto-selects `tests` when `ENVIRONMENT === 'testing'`. |
| `app/Controllers/Home.php`, `app/Views/welcome_message.php` | Dead stock CI4 code, unreachable. |
| `public/.htaccess` | Rewrite to `index.php`; requires `mod_rewrite` + `AllowOverride All`. |

---

## Suggested Reading Order

1. **`app/Config/Routes.php`** — 4 lines that define the entire surface area; start here to see how small the app really is.
2. **`app/Views/register.php`** — the DOM you'll restyle; every `id` is coupled to server error keys.
3. **`public/assets/css/register.css`** — the whole current design system in ~10 KB; you'll be replacing this file's role, not appending to it.
4. **`public/assets/js/register.js`** — carousel, role toggle, two-layer validation, and the blur-all-fields bug.
5. **`app/Controllers/Register.php`** — server rules + custom CSRF flow + the hard-coded `DEPARTMENTS` const the migration will invalidate.
6. **`app/Database/Schema/schema.sql`** — the three tables and exact constraints the new `departments`/`department_id` design must reconcile with.

---

**Next step:** I'm in **Explore Mode**, so I can't implement the redesign. Approve the plan (or adjust phases/tokens/routes) and switch to **Act Mode** via the mode selector. Implementation order will be: design tokens → landing animation → parallax → responsive fixes → database + forms, with a changed-file list and test steps after each step.