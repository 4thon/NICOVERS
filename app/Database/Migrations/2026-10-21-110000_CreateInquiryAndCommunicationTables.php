<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInquiryAndCommunicationTables extends Migration
{
    public function up(): void
    {
        $this->db->query(
            "CREATE TABLE product_inquiries (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id BIGINT UNSIGNED NOT NULL,
                user_id BIGINT UNSIGNED NULL,
                customer_name VARCHAR(150) NOT NULL,
                email VARCHAR(254) NOT NULL,
                phone VARCHAR(32) NOT NULL,
                project_address VARCHAR(500) NULL,
                message TEXT NULL,
                status ENUM('new', 'reviewing', 'quoted', 'closed') NOT NULL DEFAULT 'new',
                assigned_department ENUM('Administration', 'IT', 'Despatch', 'Accounting', 'HR', 'Marketing Sales', 'Customer Service') NOT NULL DEFAULT 'Marketing Sales',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_product_inquiries_status_department (status, assigned_department, created_at),
                CONSTRAINT fk_product_inquiries_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE RESTRICT,
                CONSTRAINT fk_product_inquiries_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE department_messages (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                sender_user_id BIGINT UNSIGNED NOT NULL,
                sender_department VARCHAR(100) NOT NULL,
                recipient_department VARCHAR(100) NOT NULL,
                subject VARCHAR(180) NOT NULL,
                body TEXT NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_department_messages_recipient_date (recipient_department, created_at),
                CONSTRAINT fk_department_messages_sender FOREIGN KEY (sender_user_id) REFERENCES users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE user_presence (
                user_id BIGINT UNSIGNED NOT NULL,
                last_seen_at DATETIME NOT NULL,
                PRIMARY KEY (user_id),
                CONSTRAINT fk_user_presence_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE user_presence, department_messages, product_inquiries');
    }
}
