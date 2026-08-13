<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Product::count() > 0) {
            return;
        }

        $productsData = [
            [
                'name' => 'Kursi Tamu Minimalis Jati',
                'description' => '<p>Kursi tamu minimalis ini terbuat dari kayu jati solid pilihan. Didesain dengan gaya modern-minimalis yang sangat cocok untuk ruang tamu masa kini. Dilengkapi dengan busa jok dudukan yang tebal dan dibalut kain tenun berkualitas tinggi yang tahan lama dan mudah dibersihkan.</p><p>Setiap detail pengerjaan dikerjakan secara hand-made oleh pengrajin profesional di Jepara, memastikan kekuatan konstruksi kayu yang sangat kokoh dan ketahanan jangka panjang.</p>',
                'short_description' => 'Set kursi tamu minimalis dari kayu jati solid dengan jok busa premium.',
                'price' => 3500000,
                'stock_status' => 'available',
                'is_published' => true,
                'category' => 'kursi_tamu',
                'images' => [
                    ['url' => 'products/product_1.webp', 'is_primary' => true],
                    ['url' => 'products/product_1_detail.webp', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'Meja Makan Kayu Jati Mewah',
                'description' => '<p>Nikmati momen bersantap bersama keluarga besar dengan Meja Makan Kayu Jati Mewah ini. Set meja makan ini terdiri dari 1 meja berukuran besar (180cm x 90cm) dan 6 kursi makan dengan sandaran yang ergonomis.</p><p>Finishing menggunakan teknik spray melamic natural doff yang memperlihatkan keindahan serat alami kayu jati asli tanpa menutup tekstur aslinya. Konstruksi kokoh dan tahan rayap.</p>',
                'short_description' => 'Set meja makan mewah kayu jati isi 6 kursi dengan finishing melamic doff.',
                'price' => 7200000,
                'stock_status' => 'preorder',
                'is_published' => true,
                'category' => 'meja_makan',
                'images' => [
                    ['url' => 'products/product_2.webp', 'is_primary' => true],
                    ['url' => 'products/product_2_detail.webp', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'Lemari Pakaian Ukir Klasik',
                'description' => '<p>Lemari pakaian klasik dengan ukiran Jepara bernilai seni tinggi. Memiliki 3 pintu utama dengan gantungan baju, laci penyimpanan barang berharga dengan kunci, serta rak-rak lipat yang luas.</p><p>Terbuat dari kayu jati tua berumur puluhan tahun, lemari ini sangat awet dan tahan lembab. Sangat cocok bagi Anda pecinta estetika seni interior tradisional Jawa klasik.</p>',
                'short_description' => 'Lemari pakaian 3 pintu jati dengan ukiran khas Jepara yang eksklusif.',
                'price' => 8900000,
                'stock_status' => 'available',
                'is_published' => true,
                'category' => 'lemari',
                'images' => [
                    ['url' => 'products/product_3.webp', 'is_primary' => true],
                    ['url' => 'products/product_3_detail.webp', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'Tempat Tidur Jati King Size',
                'description' => '<p>Hadirkan kenyamanan tidur berkelas di kamar Anda dengan Tempat Tidur King Size ini. Didesain dengan headboard tinggi bermotif minimalis geometris modern yang mempercantik tampilan kamar tidur utama Anda.</p><p>Menggunakan penopang matras berbahan kayu jati tebal yang sanggup menahan beban hingga ratusan kilogram tanpa menimbulkan bunyi derit saat digunakan tidur.</p>',
                'short_description' => 'Rangka tempat tidur king size (180x200) kayu jati bergaya minimalis modern.',
                'price' => 6500000,
                'stock_status' => 'preorder',
                'is_published' => true,
                'category' => 'tempat_tidur',
                'images' => [
                    ['url' => 'products/product_4.webp', 'is_primary' => true],
                    ['url' => 'products/product_4_detail.webp', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'Rak Buku Retro Modern',
                'description' => '<p>Rak buku serbaguna dengan desain retro modern khas tahun 70-an. Memiliki beberapa kompartemen terbuka untuk memajang buku, tanaman hias, atau koleksi foto, serta 2 laci penyimpanan tertutup di bagian bawah.</p><p>Kaki-kaki rak dibuat meruncing khas gaya Scandinavian Mid-Century. Konstruksi kayu jati berkualitas dengan pengerjaan sambungan yang rapi.</p>',
                'short_description' => 'Rak buku serbaguna bergaya Scandinavian retro dengan laci penyimpanan.',
                'price' => 2100000,
                'stock_status' => 'available',
                'is_published' => true,
                'category' => 'rak',
                'images' => [
                    ['url' => 'products/product_5.webp', 'is_primary' => true],
                    ['url' => 'products/product_5_detail.webp', 'is_primary' => false],
                ]
            ],
        ];

        foreach ($productsData as $prod) {
            $product = Product::create([
                'name' => $prod['name'],
                'slug' => Str::slug($prod['name']),
                'category' => $prod['category'] ?? 'lainnya',
                'description' => $prod['description'],
                'short_description' => $prod['short_description'],
                'price' => $prod['price'],
                'stock_status' => $prod['stock_status'],
                'is_published' => $prod['is_published'] ?? true,
            ]);

            foreach ($prod['images'] as $idx => $img) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $img['url'],
                    'is_primary' => $img['is_primary'] ?? ($idx === 0),
                    'sort_order' => $idx,
                ]);
            }
        }
    }
}
