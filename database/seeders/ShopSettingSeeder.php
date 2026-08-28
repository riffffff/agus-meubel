<?php

namespace Database\Seeders;

use App\Models\ShopSetting;
use Illuminate\Database\Seeder;

class ShopSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (ShopSetting::count() === 0) {
            ShopSetting::create([
                'shop_name' => 'Agus Mebel Jepara',
                'address' => 'Jl. Tahunan - Batealit, Jepara, Jawa Tengah 59427',
                'whatsapp_number' => '6281234567890',
                'whatsapp_template' => "Halo, saya tertarik dengan produk *{product_name}* seharga {product_price}. Apakah masih tersedia?",
                'operating_hours' => 'Senin - Sabtu: 08.00 - 17.00 WIB',
                'hero_banner_text_1' => 'Furniture Kayu Jati Premium Jepara',
                'hero_banner_text_2' => 'Sentuhan Kemewahan Alami Langsung dari Pengrajin Asli Jepara',
                'shipping_areas' => ['Seluruh Indonesia', 'Jawa Tengah', 'Jabodetabek', 'Luar Jawa'],
                'shipping_estimate_days' => '5 - 14 Hari Kerja',
            ]);
        }
    }
}
