CREATE DATABASE IF NOT EXISTS sun_son_solar
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sun_son_solar;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  first_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100) NULL,
  last_name VARCHAR(100) NOT NULL,
  birthdate DATE NOT NULL,
  gender ENUM('Female', 'Male', 'Other') NOT NULL,
  role ENUM('customer', 'employee') NOT NULL DEFAULT 'customer',
  account_status ENUM('active', 'pending') NOT NULL DEFAULT 'active',
  department ENUM('Installation', 'Maintenance and Repair', 'System Design', 'Sales and Consultation', 'Administration') NULL,
  email VARCHAR(254) NOT NULL,
  phone VARCHAR(32) NOT NULL,
  address VARCHAR(500) NOT NULL,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_username (username),
  KEY idx_users_role (role),
  CONSTRAINT chk_employee_department CHECK (role <> 'employee' OR department IS NOT NULL)
) ENGINE=InnoDB;
