<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestAdminSeeder extends Seeder
{
    public function run(): void
    {
        $account = [
            'email' => 'admin@sunsonsolar.local',
            'username' => 'admin',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'account_status' => 'active',
        ];

        $existing = $this->db->table('users')
            ->where('username', $account['username'])
            ->get()
            ->getRowArray();

        if ($existing === null) {
            $this->db->table('users')->insert($account);

            return;
        }

        $this->db->table('users')
            ->where('id', $existing['id'])
            ->update($account);
    }
}
