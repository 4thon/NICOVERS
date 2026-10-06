<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEmployeeDepartments extends Migration
{
    public function up(): void
    {
        $this->db->query(
            "ALTER TABLE employees MODIFY department ENUM(
                'Administration', 'IT', 'Despatch', 'Accounting', 'HR',
                'Marketing Sales', 'Customer Service'
            ) NOT NULL"
        );
    }

    public function down(): void
    {
        $this->db->query(
            "ALTER TABLE employees MODIFY department ENUM(
                'Installation', 'Maintenance and Repair', 'System Design',
                'Sales and Consultation', 'Administration'
            ) NOT NULL"
        );
    }
}
