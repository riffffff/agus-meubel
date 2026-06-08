<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ShopSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Agus Mebel',
            'email' => 'admin@agusmebel.com',
            'password' => Hash::make('password123'),
        ]);

        ShopSetting::create([
            'whatsapp_number' => '6281234567890',
            'whatsapp_template' => "Halo, saya tertarik dengan produk *{product_name}* seharga {product_price}. Apakah tersedia?",
        ]);
    }
}
