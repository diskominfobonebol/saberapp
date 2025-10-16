<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roles = [
            'admin',
            'user',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'nip' => '1234567890',
            'username' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');



        $user = User::create([
            'name' => 'User',
            'email' => 'user@user.com',
            'nip' => '1234567890',
            'username' => 'user',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('user');
    }
}
