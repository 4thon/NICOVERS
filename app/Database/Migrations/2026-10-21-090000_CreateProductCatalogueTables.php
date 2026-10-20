<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductCatalogueTables extends Migration
{
    public function up(): void
    {
        $this->db->query(
            "CREATE TABLE product_categories (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                code VARCHAR(50) NOT NULL,
                name VARCHAR(100) NOT NULL,
                description VARCHAR(500) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_product_categories_code (code),
                UNIQUE KEY uq_product_categories_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE products (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                category_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(180) NOT NULL,
                slug VARCHAR(200) NOT NULL,
                description TEXT NOT NULL,
                price DECIMAL(12,2) NULL,
                stock_qty INT UNSIGNED NULL,
                warranty_years SMALLINT UNSIGNED NULL,
                image_path VARCHAR(500) NULL,
                model_url VARCHAR(500) NULL,
                model_scale DECIMAL(8,3) NULL,
                is_published TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_products_slug (slug),
                KEY idx_products_category_published (category_id, is_published),
                CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES product_categories (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE product_images (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id BIGINT UNSIGNED NOT NULL,
                image_path VARCHAR(500) NOT NULL,
                alt_text VARCHAR(255) NULL,
                sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (id),
                KEY idx_product_images_product_sort (product_id, sort_order),
                CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE product_specs (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id BIGINT UNSIGNED NOT NULL,
                spec_name VARCHAR(120) NOT NULL,
                spec_value VARCHAR(500) NOT NULL,
                sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (id),
                KEY idx_product_specs_product_sort (product_id, sort_order),
                CONSTRAINT fk_product_specs_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE saved_calculations (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NULL,
                monthly_bill DECIMAL(12,2) NOT NULL,
                roof_area DECIMAL(10,2) NOT NULL,
                location VARCHAR(180) NULL,
                result_kw DECIMAL(8,2) NOT NULL,
                result_monthly_savings DECIMAL(12,2) NOT NULL,
                result_annual_savings DECIMAL(12,2) NOT NULL,
                payback_years DECIMAL(6,2) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_saved_calculations_user_date (user_id, created_at),
                CONSTRAINT fk_saved_calculations_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "CREATE TABLE monitoring_readings (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                service_request_id BIGINT UNSIGNED NOT NULL,
                reading_date DATE NOT NULL,
                kwh_generated DECIMAL(12,2) NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_monitoring_readings_request_date (service_request_id, reading_date),
                CONSTRAINT fk_monitoring_readings_request FOREIGN KEY (service_request_id) REFERENCES service_requests (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $this->db->query(
            "INSERT INTO product_categories (code, name, description) VALUES
                ('panels', 'Solar Panels', 'Solar panel options for residential and commercial energy systems.'),
                ('inverters', 'Inverters', 'Equipment that converts solar energy into usable electricity.'),
                ('batteries', 'Batteries', 'Energy storage options for solar system resilience.'),
                ('mounting', 'Racking and Mounting', 'Mounting components designed to support solar panel installation.'),
                ('wires', 'Solar Wires', 'Solar-ready wiring and cable components for system connections.')"
        );
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE monitoring_readings, saved_calculations, product_specs, product_images, products, product_categories');
    }
}
