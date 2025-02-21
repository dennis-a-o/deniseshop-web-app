<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'username' => 'Admin',
            'first_name' => 'John',
            'last_name' => 'Admin',
            'email' => 'admin@localhost.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'image' => '',
            'created_at' => now()
        ]);
        DB::table('users')->insert([
            'username' => 'User',
            'first_name' => 'John',
            'last_name' => 'User',
            'email' => 'user@localhost.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            #'email_verified_at' => now(),
            'image' => '',
            'created_at' => now()
        ]);
    }
}
