<?php

namespace Database\Seeders;

use App\Models\StaffMember;
use App\Models\User;
use App\Support\OtDutyOptions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@dy.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@dy.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]
        );

        User::firstOrCreate(
            ['email' => 'superadmin@dy.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ]
        );

        foreach (StaffMember::TYPES as $type) {
            foreach (OtDutyOptions::sisters() as $name) {
                StaffMember::firstOrCreate(['type' => $type, 'name' => $name]);
            }
        }
    }
}