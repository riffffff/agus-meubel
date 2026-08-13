<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Review::count() > 0) {
            return;
        }

        $products = Product::all();

        $reviewsData = [
            [
                'product_index' => 0,
                'name' => 'Budi Santoso',
                'city' => 'Semarang',
                'rating' => 5,
                'review' => 'Kualitas kayu jati di Agus Mebel benar-benar luar biasa! Kursi tamu minimalis yang saya beli pengerjaannya sangat halus dan kayunya kokoh sekali. Sangat direkomendasikan!',
                'is_approved' => true,
            ],
            [
                'product_index' => 1,
                'name' => 'Dewi Lestari',
                'city' => 'Jakarta Selatan',
                'rating' => 5,
                'review' => 'Pre-order meja makan kayu jati di sini selesai tepat waktu. Hasil finishing-nya doff natural sangat rapi dan sesuai ekspektasi. Pelayanan admin via WhatsApp juga ramah dan responsif.',
                'is_approved' => true,
            ],
            [
                'product_index' => 2,
                'name' => 'Ahmad Fauzi',
                'city' => 'Surabaya',
                'rating' => 4,
                'review' => 'Barang sampai dengan selamat di Surabaya dengan packing kayu tebal. Kualitas ukiran lemarinya sangat bernilai seni. Sangat puas dengan keindahan produknya.',
                'is_approved' => true,
            ],
            [
                'product_index' => 4,
                'name' => 'Siti Aminah',
                'city' => 'Yogyakarta',
                'rating' => 5,
                'review' => 'Rak buku Scandi-nya cantik sekali! Pas dipajang di ruang tamu minimalis saya. Harganya sangat bersahabat untuk kualitas kayu jati asli Jepara. Sukses terus Agus Mebel!',
                'is_approved' => true,
            ],
        ];

        foreach ($reviewsData as $rev) {
            $product = $products->get($rev['product_index']);
            Review::create([
                'product_id' => $product ? $product->id : null,
                'name' => $rev['name'],
                'city' => $rev['city'],
                'rating' => $rev['rating'],
                'review' => $rev['review'],
                'is_approved' => $rev['is_approved'],
            ]);
        }
    }
}
