# Sun Son Solar System Design

## Access rules

- Customers may create their own account and request a quote or service.
- HR and administrators create employee accounts; employee registration is never public.
- Admin accounts manage users, service settings, prices, availability, notifications, and reports.
- Dispatchers work from the service request calendar and assign technicians.
- Technicians only receive the customer and job information needed for their assigned services.

## Customer portal

The customer dashboard is designed around `service_requests`, `notifications`, `user_preferences`, `invoices`, `payments`, and `support_tickets`.

- Schedule: upcoming consultations, installations, and maintenance visits.
- Notifications: reminders, schedule changes, promotions, and system updates, controlled by preferences.
- Profile: customer details, password controls, and notification preferences.
- History: completed services, invoices, payments, and receipts.
- Support: ticket and message history.

## Operations portal

- User management: administrators add, edit, activate, deactivate, and assign roles.
- Schedule overview: admins and Despatch users approve, decline, schedule, and assign `service_requests`.
- Reports: customer activity, service volumes, revenue, and field report data can be aggregated from the operational tables.
- Notification control: administrators publish audience-specific notifications.
- Settings: service categories, prices, availability, and other configurable keys use `system_settings`.

## Technician workspace

- Assigned jobs use `service_requests.assigned_employee_id`.
- Arrival check-in uses `technician_checkins` with time, coordinates, and accuracy.
- Job issues, customer concerns, safety items, and completion notes use `technician_service_reports`.
- Technician access must scope customer information to assigned jobs only.

## Security rules

- Passwords use PHP `password_hash()` and are never stored in plaintext.
- Public registration and quote submissions use single-use, session-backed CSRF tokens.
- Server-side validation is required even when client-side validation is present.
- Authorization checks must be enforced in future dashboard controllers, not only hidden in the UI.
