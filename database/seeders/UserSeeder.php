<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::where('email', 'admin@agusmebel.com')->doesntExist()) {
            User::create([
                'name'     => 'Admin Agus Mebel',
                'email'    => 'admin@agusmebel.com',
                'password' => Hash::make('password123'),
                'is_admin' => true,
            ]);
        } else {
            // Pastikan user admin yang sudah ada punya flag is_admin = true
            User::where('email', 'admin@agusmebel.com')
                ->update(['is_admin' => true]);
        }
    }
}
