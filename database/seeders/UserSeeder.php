<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private $users = [
        [
            'name' => 'Anggit',
            'email' => 'anggit@employee.icp',
            'password' => 'password',
        ],
        [
            'name' => 'Tri',
            'email' => 'tri@employee.icp',
            'password' => 'password',
        ],
        [
            'name' => 'Banu',
            'email' => 'banu@employee.icp',
            'password' => 'password',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->users as $user) {
            User::factory()->create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt($user['password']),
            ]);
        }
    }
}
