<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Article::count() > 0) {
            return;
        }

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
                'is_published' => $art['is_published'],
                'published_at' => $art['published_at'],
            ]);
        }
    }
}
