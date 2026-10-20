CREATE DATABASE IF NOT EXISTS sun_son_solar
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sun_son_solar;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(254) NOT NULL,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer', 'employee', 'admin') NOT NULL,
  account_status ENUM('active', 'pending', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_username (username),
  KEY idx_users_role (role)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customers (
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
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS employees (
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
) ENGINE=InnoDB;

-- Operational tables for scheduling, support, notifications, payments,
-- technician activity, and administrator settings are created by the
-- CodeIgniter migration: 2026-10-20-090000_CreateOperationsTables.php.
-- Run `php spark migrate` instead of importing this file alone for a full setup.
