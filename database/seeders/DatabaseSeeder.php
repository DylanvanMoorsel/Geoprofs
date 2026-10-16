<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $planning = Department::create(['name' => 'Planning']);
        Department::create(['name' => 'Techniek']);

        $manager = User::create([
            'first_name' => 'Manon',
            'last_name' => 'Manager',
            'email' => 'manager@geoprofs.nl',
            'password' => 'password123',
            'role' => 'manager',
            'department_id' => $planning->id,
            'vacation_hours' => 200,
        ]);

        User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@geoprofs.nl',
            'password' => 'password123',
            'role' => 'employee',
            'department_id' => $planning->id,
            'manager_id' => $manager->id,
            'vacation_hours' => 176,
        ]);
    }
}