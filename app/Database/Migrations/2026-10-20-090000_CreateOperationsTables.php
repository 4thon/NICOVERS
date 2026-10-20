<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperationsTables extends Migration
{
    public function up(): void
    {
        $this->db->query("ALTER TABLE users MODIFY role ENUM('customer', 'employee', 'admin') NOT NULL");
        $this->db->query("ALTER TABLE users MODIFY account_status ENUM('active', 'pending', 'inactive') NOT NULL DEFAULT 'active'");

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS service_categories (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                code VARCHAR(50) NOT NULL,
                name VARCHAR(100) NOT NULL,
                description VARCHAR(500) NULL,
                base_price DECIMAL(12,2) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_service_categories_code (code)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS service_requests (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NULL,
                category_id BIGINT UNSIGNED NOT NULL,
                customer_name VARCHAR(150) NOT NULL,
                email VARCHAR(254) NOT NULL,
                phone VARCHAR(32) NOT NULL,
                address VARCHAR(500) NOT NULL,
                preferred_date DATE NULL,
                details TEXT NULL,
                status ENUM('pending', 'approved', 'declined', 'scheduled', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
                scheduled_at DATETIME NULL,
                assigned_employee_id BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_requests_status_date (status, scheduled_at),
                KEY idx_requests_user_id (user_id),
                KEY idx_requests_employee_id (assigned_employee_id),
                CONSTRAINT fk_requests_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
                CONSTRAINT fk_requests_category FOREIGN KEY (category_id) REFERENCES service_categories (id),
                CONSTRAINT fk_requests_employee FOREIGN KEY (assigned_employee_id) REFERENCES users (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS notifications (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                title VARCHAR(160) NOT NULL,
                body TEXT NOT NULL,
                type ENUM('reminder', 'promotion', 'schedule', 'system', 'safety') NOT NULL DEFAULT 'system',
                audience ENUM('customers', 'employees', 'all') NOT NULL DEFAULT 'all',
                action_url VARCHAR(500) NULL,
                created_by BIGINT UNSIGNED NULL,
                published_at DATETIME NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_notifications_audience_published (audience, published_at),
                CONSTRAINT fk_notifications_author FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS user_notifications (
                user_id BIGINT UNSIGNED NOT NULL,
                notification_id BIGINT UNSIGNED NOT NULL,
                read_at DATETIME NULL,
                PRIMARY KEY (user_id, notification_id),
                CONSTRAINT fk_user_notifications_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                CONSTRAINT fk_user_notifications_notification FOREIGN KEY (notification_id) REFERENCES notifications (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS user_preferences (
                user_id BIGINT UNSIGNED NOT NULL,
                schedule_reminders TINYINT(1) NOT NULL DEFAULT 1,
                promotions TINYINT(1) NOT NULL DEFAULT 1,
                system_updates TINYINT(1) NOT NULL DEFAULT 1,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (user_id),
                CONSTRAINT fk_preferences_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS support_tickets (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                subject VARCHAR(180) NOT NULL,
                category ENUM('account', 'billing', 'service', 'technical', 'other') NOT NULL DEFAULT 'other',
                priority ENUM('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal',
                status ENUM('open', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'open',
                assigned_to BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_tickets_status_assignee (status, assigned_to),
                CONSTRAINT fk_tickets_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                CONSTRAINT fk_tickets_assignee FOREIGN KEY (assigned_to) REFERENCES users (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS support_ticket_messages (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                ticket_id BIGINT UNSIGNED NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_ticket_messages_ticket (ticket_id, created_at),
                CONSTRAINT fk_ticket_messages_ticket FOREIGN KEY (ticket_id) REFERENCES support_tickets (id) ON DELETE CASCADE,
                CONSTRAINT fk_ticket_messages_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS invoices (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                service_request_id BIGINT UNSIGNED NOT NULL,
                invoice_number VARCHAR(50) NOT NULL,
                amount DECIMAL(12,2) NOT NULL,
                status ENUM('draft', 'issued', 'paid', 'void', 'overdue') NOT NULL DEFAULT 'draft',
                issued_at DATETIME NULL,
                due_at DATE NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_invoices_number (invoice_number),
                KEY idx_invoices_request (service_request_id),
                CONSTRAINT fk_invoices_request FOREIGN KEY (service_request_id) REFERENCES service_requests (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS payments (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                invoice_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(12,2) NOT NULL,
                method VARCHAR(50) NOT NULL,
                reference_number VARCHAR(100) NULL,
                paid_at DATETIME NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_payments_invoice (invoice_id),
                CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS technician_checkins (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                service_request_id BIGINT UNSIGNED NOT NULL,
                employee_user_id BIGINT UNSIGNED NOT NULL,
                latitude DECIMAL(10,7) NOT NULL,
                longitude DECIMAL(10,7) NOT NULL,
                accuracy_m DECIMAL(8,2) NULL,
                checked_in_at DATETIME NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_checkins_request_employee (service_request_id, employee_user_id),
                CONSTRAINT fk_checkins_request FOREIGN KEY (service_request_id) REFERENCES service_requests (id) ON DELETE CASCADE,
                CONSTRAINT fk_checkins_employee FOREIGN KEY (employee_user_id) REFERENCES users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS technician_service_reports (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                service_request_id BIGINT UNSIGNED NOT NULL,
                employee_user_id BIGINT UNSIGNED NOT NULL,
                report_type ENUM('completion', 'issue', 'customer_concern', 'safety') NOT NULL,
                summary TEXT NOT NULL,
                submitted_at DATETIME NOT NULL,
                reviewed_by BIGINT UNSIGNED NULL,
                reviewed_at DATETIME NULL,
                PRIMARY KEY (id),
                KEY idx_reports_request_type (service_request_id, report_type),
                CONSTRAINT fk_reports_request FOREIGN KEY (service_request_id) REFERENCES service_requests (id) ON DELETE CASCADE,
                CONSTRAINT fk_reports_employee FOREIGN KEY (employee_user_id) REFERENCES users (id) ON DELETE CASCADE,
                CONSTRAINT fk_reports_reviewer FOREIGN KEY (reviewed_by) REFERENCES users (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS system_settings (
                setting_key VARCHAR(100) NOT NULL,
                setting_value TEXT NOT NULL,
                updated_by BIGINT UNSIGNED NULL,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (setting_key),
                CONSTRAINT fk_settings_editor FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "INSERT INTO service_categories (code, name, description) VALUES
                ('consultation', 'Solar Consultation', 'A tailored assessment of your energy needs and solar options.'),
                ('installation', 'Solar Installation', 'Professional rooftop solar system installation.'),
                ('maintenance', 'Maintenance and Repair', 'Inspection, monitoring, maintenance, and service support.')
            ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)"
        );
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS system_settings');
        $this->db->query('DROP TABLE IF EXISTS technician_service_reports');
        $this->db->query('DROP TABLE IF EXISTS technician_checkins');
        $this->db->query('DROP TABLE IF EXISTS payments');
        $this->db->query('DROP TABLE IF EXISTS invoices');
        $this->db->query('DROP TABLE IF EXISTS support_ticket_messages');
        $this->db->query('DROP TABLE IF EXISTS support_tickets');
        $this->db->query('DROP TABLE IF EXISTS user_preferences');
        $this->db->query('DROP TABLE IF EXISTS user_notifications');
        $this->db->query('DROP TABLE IF EXISTS notifications');
        $this->db->query('DROP TABLE IF EXISTS service_requests');
        $this->db->query('DROP TABLE IF EXISTS service_categories');
        $this->db->query("ALTER TABLE users MODIFY role ENUM('customer', 'employee') NOT NULL");
        $this->db->query("ALTER TABLE users MODIFY account_status ENUM('active', 'pending') NOT NULL DEFAULT 'active'");
    }
}
