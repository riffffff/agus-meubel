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
        if (User::where('email', 'agusgerobakweb@gmail.com')->doesntExist()) {
            User::create([
                'name'     => 'Admin Agus Mebel',
                'email'    => 'agusgerobakweb@gmail.com',
                'password' => Hash::make('password123'),
                'is_admin' => true,
            ]);
        } else {
            // Pastikan user admin yang sudah ada punya flag is_admin = true
            User::where('email', 'agusgerobakweb@gmail.com')
                ->update(['is_admin' => true]);
        }
    }
}
