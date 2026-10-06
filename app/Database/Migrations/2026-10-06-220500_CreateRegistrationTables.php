<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegistrationTables extends Migration
{
    public function up(): void
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                email VARCHAR(254) NOT NULL,
                username VARCHAR(50) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                role ENUM('customer', 'employee') NOT NULL,
                account_status ENUM('active', 'pending') NOT NULL DEFAULT 'active',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_users_email (email),
                UNIQUE KEY uq_users_username (username),
                KEY idx_users_role (role)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS customers (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                middle_name VARCHAR(100) NULL,
                last_name VARCHAR(100) NOT NULL,
                birthdate DATE NOT NULL,
                gender ENUM('Female', 'Male', 'Other') NOT NULL,
                phone VARCHAR(32) NOT NULL,
                address VARCHAR(500) NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_customers_user_id (user_id),
                CONSTRAINT fk_customers_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS employees (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                middle_name VARCHAR(100) NULL,
                last_name VARCHAR(100) NOT NULL,
                birthdate DATE NOT NULL,
                gender ENUM('Female', 'Male', 'Other') NOT NULL,
                department ENUM('Administration', 'IT', 'Despatch', 'Accounting', 'HR', 'Marketing Sales', 'Customer Service') NOT NULL,
                phone VARCHAR(32) NOT NULL,
                address VARCHAR(500) NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_employees_user_id (user_id),
                KEY idx_employees_department (department),
                CONSTRAINT fk_employees_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS employees');
        $this->db->query('DROP TABLE IF EXISTS customers');
        $this->db->query('DROP TABLE IF EXISTS users');
    }
}
