<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ShopSetting;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Article;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        User::create([
            'name' => 'Admin Agus Mebel',
            'email' => 'admin@agusmebel.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Shop Settings
        ShopSetting::create([
            'whatsapp_number' => '6281234567890',
            'whatsapp_template' => "Halo, saya tertarik dengan produk *{product_name}* seharga {product_price}. Apakah tersedia?",
        ]);

        // 3. Create 5 Products
        $productsData = [
            [
                'name' => 'Kursi Tamu Minimalis Jati',
                'description' => '<p>Kursi tamu minimalis ini terbuat dari kayu jati solid pilihan. Didesain dengan gaya modern-minimalis yang sangat cocok untuk ruang tamu masa kini. Dilengkapi dengan busa jok dudukan yang tebal dan dibalut kain tenun berkualitas tinggi yang tahan lama dan mudah dibersihkan.</p><p>Setiap detail pengerjaan dikerjakan secara hand-made oleh pengrajin profesional di Jepara, memastikan kekuatan konstruksi kayu yang sangat kokoh dan ketahanan jangka panjang.</p>',
                'short_description' => 'Set kursi tamu minimalis dari kayu jati solid dengan jok busa premium.',
                'price' => 3500000,
                'stock_status' => 'available',
                'is_published' => true,
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
                'images' => [
                    ['url' => 'products/product_5.webp', 'is_primary' => true],
                    ['url' => 'products/product_5_detail.webp', 'is_primary' => false],
                ]
            ],
        ];

        $products = [];
        foreach ($productsData as $prod) {
            $product = Product::create([
                'name' => $prod['name'],
                'slug' => Str::slug($prod['name']),
                'description' => $prod['description'],
                'short_description' => $prod['short_description'],
                'price' => $prod['price'],
                'stock_status' => $prod['stock_status'],
                'is_published' => $prod['is_published'] ?? true,
            ]);
            $products[] = $product;

            foreach ($prod['images'] as $idx => $img) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $img['url'],
                    'is_primary' => $img['is_primary'] ?? ($idx === 0),
                    'sort_order' => $idx,
                ]);
            }
        }

        // 4. Create 5 Articles (exactly 3 are hero articles, adhering to the max 3 business rule)
        $articlesData = [
            [
                'title' => 'Tips Merawat Furniture Kayu Jati Agar Tahan Lama',
                'content' => '<h2>Merawat Furniture Kayu Jati Kesayangan Anda</h2><p>Kayu jati memang dikenal sebagai salah satu material furniture terkuat dan paling awet di dunia. Namun, agar kilau alaminya tetap terjaga sepanjang masa, diperlukan tips perawatan berkala yang tepat.</p><p>Berikut adalah tips praktis yang bisa Anda lakukan di rumah:</p><ul><li>Hindari paparan sinar matahari secara langsung terus-menerus agar warna tidak memudar.</li><li>Bersihkan debu setiap hari dengan kain lap microfiber setengah basah.</li><li>Gunakan minyak khusus jati (teak oil) setiap 6 bulan sekali untuk merawat kelembaban serat kayu.</li></ul>',
                'excerpt' => 'Panduan lengkap merawat keindahan furniture kayu jati Anda agar tetap mengkilap dan bebas rayap.',
                'image' => 'articles/article_1.webp',
                'is_hero' => true,
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Tren Desain Interior Rumah Minimalis Tahun 2026',
                'content' => '<h2>Mengenal Tren Desain Interior Tahun 2026</h2><p>Tahun ini, tren interior rumah beralih ke gaya "Warm Minimalist" yang menggabungkan kepraktisan hidup modern dengan kehangatan elemen alam. Furniture dengan bahan kayu jati mentah atau finishing natural doff menjadi primadona baru.</p><p>Simak bagaimana memadukan warna dinding netral dengan aksen kayu jati untuk menciptakan ruang yang tenang namun tetap estetis.</p>',
                'excerpt' => 'Kombinasi warna kayu hangat dan ruang fungsional mendominasi tren desain rumah tahun ini.',
                'image' => 'articles/article_2.webp',
                'is_hero' => true,
                'is_published' => true,
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Keunggulan Kayu Jati Jepara Dibanding Kayu Lainnya',
                'content' => '<h2>Mengapa Harus Memilih Jati Jepara?</h2><p>Jepara bukan sekadar kota ukir, melainkan pusat peradaban kerajinan kayu jati terbaik di Asia Tenggara. Kayu jati Jepara memiliki kandungan minyak alami yang tinggi yang membuatnya kebal dari serangan rayap maupun jamur meskipun di cuaca ekstrim.</p><p>Artikel ini merangkum perbandingan lengkap kekuatan jati jepara dengan jati daerah lain serta jenis kayu lunak lainnya.</p>',
                'excerpt' => 'Alasan utama mengapa kayu jati asal Jepara dinilai lebih unggul dalam konstruksi furniture premium.',
                'image' => 'articles/article_3.webp',
                'is_hero' => true,
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Cara Memilih Warna Furniture Sesuai Cat Dinding',
                'content' => '<h2>Paduan Harmonis Antara Furniture dan Cat Dinding</h2><p>Banyak pemilik rumah kebingungan memadukan warna cat dinding dengan furniture yang mereka beli. Padahal, kuncinya terletak pada color palette roda warna.</p><p>Kami membagikan skema warna terbaik untuk mendampingi furniture kayu cokelat gelap agar ruangan tidak terkesan gelap atau sempit.</p>',
                'excerpt' => 'Tips memadukan warna cat dinding rumah Anda dengan furniture kayu bertema warm earth tone.',
                'image' => 'articles/article_4.webp',
                'is_hero' => false,
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Panduan Menata Ruang Tamu Sempit Agar Terlihat Luas',
                'content' => '<h2>Menyiasati Ruang Tamu Mungil</h2><p>Memiliki ruang tamu berukuran kecil tidak berarti Anda harus mengorbankan estetika dan kenyamanan. Dengan penataan letak sofa yang strategis dan pemilihan furniture berkaki tinggi (retro/Scandinavian), Anda bisa memberikan ilusi ruangan yang lapang.</p>',
                'excerpt' => 'Trik cerdas memilih dan menata furniture untuk ruang tamu mungil agar terasa lebih lega.',
                'image' => 'articles/article_5.webp',
                'is_hero' => false,
                'is_published' => true,
                'published_at' => now()->subDays(4),
            ],
        ];

        foreach ($articlesData as $art) {
            Article::create([
                'title' => $art['title'],
                'slug' => Str::slug($art['title']),
                'content' => $art['content'],
                'excerpt' => $art['excerpt'],
                'image' => $art['image'],
                'is_hero' => $art['is_hero'],
                'published_at' => $art['published_at'],
            ]);
        }

        // 5. Create 4 Sample Reviews (terhubung ke produk yang baru dibuat)
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
                'review' => 'Barang sampai dengan selamat di Surabaya dengan packing kayu tebal. Kualitas ukiran lemarinya sangat bernilai seni. Hanya saja pengiriman cargo agak lambat sehari dari jadwal.',
                'is_approved' => true,
            ],
            [
                'product_index' => 4,
                'name' => 'Siti Aminah',
                'city' => 'Yogyakarta',
                'rating' => 5,
                'review' => 'Rak buku Scandi-nya cantik sekali! Pas dipajang di ruang tamu minimalis saya. Harganya sangat bersahabat untuk kualitas kayu jati asli Jepara. Sukses terus Agus Mebel!',
                'is_approved' => true,
            ]
        ];

        foreach ($reviewsData as $rev) {
            $productIdx = $rev['product_index'];
            $productId = isset($products[$productIdx]) ? $products[$productIdx]->id : null;
            Review::create([
                'product_id' => $productId,
                'name' => $rev['name'],
                'city' => $rev['city'],
                'rating' => $rev['rating'],
                'review' => $rev['review'],
                'is_approved' => $rev['is_approved'],
            ]);
        }
    }
}
