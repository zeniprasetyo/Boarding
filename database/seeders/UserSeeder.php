<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan roles sudah ada
        $roles = ['admin', 'musyrif', 'santri'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@boarding.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Musyrif
        $musyrif = User::firstOrCreate(
            ['email' => 'musyrif@boarding.test'],
            [
                'name' => 'Ustadz Musyrif',
                'password' => Hash::make('password'),
            ]
        );
        $musyrif->assignRole('musyrif');

        // Santri
        $santri = User::firstOrCreate(
            ['email' => 'santri@boarding.test'],
            [
                'name' => 'Santri 1',
                'password' => Hash::make('password'),
            ]
        );
        $santri->assignRole('santri');
    }
}
