<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClientEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [
                'email' => 'katherine.sinagaraw@sunsonsolar.com',
                'username' => 'katherine.sinagaraw',
                'first_name' => 'Katherine',
                'middle_name' => null,
                'last_name' => 'Sinagaraw',
                // Temporary values until the client provides the remaining required details.
                'birthdate' => '1900-01-01',
                'gender' => 'Other',
                'department' => 'Administration',
                'phone' => '09291230983',
                'address' => 'Sun Son Solar Office - details pending',
            ],
            [
                'email' => 'sol.solis@sunsonsolar.com',
                'username' => 'sol.sun.solis',
                'first_name' => 'Sol',
                'middle_name' => 'Sun',
                'last_name' => 'Solis',
                'birthdate' => '1967-01-08',
                'gender' => 'Other',
                'department' => 'Administration',
                'phone' => '09291230983',
                'address' => 'Sun Son Solar Office - details pending',
            ],
        ];

        foreach ($employees as $employee) {
            $user = $this->db->table('users')
                ->where('email', $employee['email'])
                ->get()
                ->getRowArray();

            if ($user === null) {
                $this->db->table('users')->insert([
                    'email' => $employee['email'],
                    'username' => $employee['username'],
                    'password_hash' => password_hash(bin2hex(random_bytes(24)), PASSWORD_DEFAULT),
                    'role' => 'employee',
                    'account_status' => 'pending',
                ]);

                $userId = (int) $this->db->insertID();
            } else {
                $userId = (int) $user['id'];
            }

            $profile = [
                'user_id' => $userId,
                'first_name' => $employee['first_name'],
                'middle_name' => $employee['middle_name'],
                'last_name' => $employee['last_name'],
                'birthdate' => $employee['birthdate'],
                'gender' => $employee['gender'],
                'department' => $employee['department'],
                'phone' => $employee['phone'],
                'address' => $employee['address'],
            ];

            $exists = $this->db->table('employees')
                ->where('user_id', $userId)
                ->countAllResults() > 0;

            if ($exists) {
                $this->db->table('employees')->where('user_id', $userId)->update($profile);
            } else {
                $this->db->table('employees')->insert($profile);
            }
        }
    }
}
