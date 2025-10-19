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

        if (env('APP_ENV') == 'production') {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'nip' => '1234567890',
                'username' => 'admin',
                'password' => Hash::make('adminmotayada2025#'),
            ]);

            $user = User::create([
                'name' => 'User',
                'email' => 'user@user.com',
                'nip' => '1234567890',
                'username' => 'user',
                'password' => Hash::make('adminmotayada2025#'),
            ]);
        } else {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'nip' => '1234567890',
                'username' => 'admin',
                'password' => Hash::make('password'),
            ]);

            $user = User::create([
                'name' => 'User',
                'email' => 'user@user.com',
                'nip' => '1234567890',
                'username' => 'user',
                'password' => Hash::make('password'),
            ]);
        }


        $admin->assignRole('admin');
        $user->assignRole('user');
    }
}
