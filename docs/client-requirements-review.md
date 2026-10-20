# Client Requirements Review

## Adopted Architecture

The active project remains a CodeIgniter 4 application on XAMPP. The supplied plain-PHP folder structure is translated into CodeIgniter controllers and views while preserving the existing database, registration flow, security conventions, and established Sun Son Solar interface.

## Confirmed Rules

- Customers may register through the public site and begin with an active account.
- Employees never register publicly. An administrator or HR user creates, activates, updates, or deactivates them.
- Login accepts username or email plus password. It reads role data from the server-side `users` record; role is never submitted by the browser.
- Administrator, HR, dispatcher, technician, and customer capabilities are checked on every protected action.
- Quotes, service bookings, invoices, and payment records are internal records. The system does not collect online payments or include e-commerce.
- Technician job views expose only the customer name, service address, and service type required for the assigned work.

## Role Model

`users.role` identifies the account group: `customer`, `employee`, or `admin`.

The current database provides the account groups `customer`, `employee`, and `admin`. Page layouts will reflect the interview's HR, dispatch, and technician workspaces without changing the stored schema. A future database change can add more granular employee access only after client approval.

## Data Decisions

- The existing department labels and current accounts remain unchanged.
- Sol Solis is the intended initial administrator, but an administrator password is not written to source code or seeded into the database. An authorized owner must set a real password during one-time setup.
- Katherine's current account category must be confirmed before assigning administrative or customer access; the system does not silently change her existing account.
- No demo or sample account is included in the schema, seeders, or interface.

## Implementation Coverage

| Area | Current foundation | Remaining feature work |
|---|---|---|
| Public site | Homepage, quote request, customer registration, login UI | DB-powered FAQ, testimonials, blog views |
| Authentication | Password hash verification, CSRF, session regeneration, account status check | Login throttling, password change, reset token delivery |
| Customer portal | Protected role session and service summary | Booking, reschedule/cancel, support tickets, receipts, preferences |
| Administration | Protected portal and employee account form | Full user management, scheduler, reports, content/settings controls |
| Field staff | Existing employee access and service-assignment foundation | Technician dashboard and geolocation interactions |
| Auditability | Existing application security and status fields | Any audit extension requires separate database approval |

## Client Items Still Requiring Confirmation

1. Sol Solis's final email address and the authorized first-admin password.
2. Katherine Sinagaraw's actual account category and access level.
3. Apollo Santos's full profile, username, and technician/dispatcher access arrangement.
4. Final service categories, base prices, availability hours, and booking time slots.
5. Approved social media links, privacy notice, terms of use, and business hours.
