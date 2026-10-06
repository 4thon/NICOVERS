CREATE DATABASE IF NOT EXISTS sun_son_solar
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sun_son_solar;

CREATE TABLE IF NOT EXISTS users (
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
  department ENUM('Installation', 'Maintenance and Repair', 'System Design', 'Sales and Consultation', 'Administration') NOT NULL,
  phone VARCHAR(32) NOT NULL,
  address VARCHAR(500) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_employees_user_id (user_id),
  KEY idx_employees_department (department),
  CONSTRAINT fk_employees_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (email, username, password_hash, role, account_status)
VALUES
  ('customer.demo@sunsonsolar.local', 'sunson.customer', '$2y$12$LhSI3gyVP/9YE2FwAXgp8OsB3sUGKSfz8Ii.AQBftlw0enAJtSGPG', 'customer', 'active'),
  ('employee.demo@sunsonsolar.local', 'sunson.employee', '$2y$12$LhSI3gyVP/9YE2FwAXgp8OsB3sUGKSfz8Ii.AQBftlw0enAJtSGPG', 'employee', 'pending')
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO customers (user_id, first_name, middle_name, last_name, birthdate, gender, phone, address)
SELECT id, 'Sample', NULL, 'Customer', '2000-01-01', 'Other', '+63 900 000 0000', 'Sun Son Solar Demo Address'
FROM users
WHERE username = 'sunson.customer'
ON DUPLICATE KEY UPDATE user_id = user_id;

INSERT INTO employees (user_id, first_name, middle_name, last_name, birthdate, gender, department, phone, address)
SELECT id, 'Sample', NULL, 'Employee', '2000-01-01', 'Other', 'Administration', '+63 900 000 0001', 'Sun Son Solar Demo Address'
FROM users
WHERE username = 'sunson.employee'
ON DUPLICATE KEY UPDATE user_id = user_id;
