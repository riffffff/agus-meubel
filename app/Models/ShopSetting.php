<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @mixin Builder
 */
class ShopSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'logo',
        'logo_dark',
        'favicon',
        'address',
        'whatsapp_number',
        'whatsapp_template',
        'operating_hours',
        'hero_banner_text_1',
        'hero_banner_text_2',
        'hero_banner_bg',
        'shipping_areas',
        'shipping_estimate_days',
    ];

    protected $casts = [
        'shipping_areas' => 'array',
    ];

    public const SINGLETON_ID = 1;

    /** Cache key dan TTL (detik) */
    public const CACHE_KEY = 'shop_settings';
    public const CACHE_TTL = 600; // 10 menit

    public function save(array $options = []): bool
    {
        $this->id = self::SINGLETON_ID;
        $this->applyDefaults();

        try {
            $existsNow = DB::table($this->getTable())->where('id', self::SINGLETON_ID)->exists();
        } catch (\Throwable) {
            $existsNow = false;
        }
        $this->exists = $existsNow;

        $result = parent::save($options);

        // Hapus cache agar data terbaru diambil pada request berikutnya
        Cache::forget(self::CACHE_KEY);

        return $result;
    }

    private function applyDefaults(): void
    {
        if (empty($this->whatsapp_number)) {
            $this->whatsapp_number = '6281234567890';
        }
        $this->whatsapp_number = preg_replace('/[^0-9]/', '', (string) $this->whatsapp_number);
        if (str_starts_with($this->whatsapp_number, '0')) {
            $this->whatsapp_number = '62' . substr($this->whatsapp_number, 1);
        }
        if (str_starts_with($this->whatsapp_number, '8')) {
            $this->whatsapp_number = '62' . $this->whatsapp_number;
        }

        if (empty($this->whatsapp_template)) {
            $this->whatsapp_template = 'Halo, saya tertarik dengan produk *{product_name}* seharga {product_price}. Apakah tersedia?';
        }

        if (empty($this->shop_name)) {
            $this->shop_name = 'Agus Mebel Jepara';
        }
    }

    /**
     * Ambil setting toko. Jika belum ada di DB, buat dengan nilai default.
     * Hasilnya dicache selama CACHE_TTL detik agar tidak ada DB write tiap request.
     */
    public static function getSettings(): self
    {
        // Coba ambil dari cache terlebih dahulu
        $cached = Cache::get(self::CACHE_KEY);
        if ($cached instanceof self) {
            return $cached;
        }

        $settings = static::query()->find(self::SINGLETON_ID);

        if ($settings === null) {
            // Hanya tulis ke DB satu kali jika baris benar-benar belum ada
            $settings = new static();
            $settings->forceFill([
                'id'                    => self::SINGLETON_ID,
                'shop_name'             => 'Agus Mebel Jepara',
                'logo'                  => null, // Admin bisa upload, fallback ke public/storage/logo/logo.jpeg
                'logo_dark'             => null,
                'favicon'               => null,
                'address'               => 'Jepara, Jawa Tengah, Indonesia',
                'whatsapp_number'       => '6281234567890',
                'whatsapp_template'     => 'Halo, saya tertarik dengan produk *{product_name}* seharga {product_price}. Apakah tersedia?',
                'operating_hours'       => 'Senin - Sabtu: 08:00 - 17:00',
                'hero_banner_text_1'    => 'Furniture Kayu Jati Premium',
                'hero_banner_text_2'    => 'Kualitas Terbaik Langsung dari Pengrajin Jepara',
                'hero_banner_bg'        => null,
                'shipping_areas'        => ['Seluruh Indonesia'],
                'shipping_estimate_days'=> '7 - 14 hari kerja',
            ]);
            $settings->save(); // save() sudah memanggil Cache::forget di atas
        }

        // Simpan ke cache
        Cache::put(self::CACHE_KEY, $settings, self::CACHE_TTL);

        return $settings;
    }

    /**
     * Hapus cache setting toko secara manual.
     * Bisa dipanggil dari luar setelah ada perubahan.
     */
    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get logo URL dengan fallback ke default.
     * 
     * @param string $mode 'light' | 'dark'
     * @return string
     */
    public function getLogoUrl(string $mode = 'light'): string
    {
        $logoField = $mode === 'dark' ? 'logo_dark' : 'logo';
        
        // Jika ada logo dari admin, gunakan itu
        if (!empty($this->$logoField)) {
            return asset('storage/' . $this->$logoField);
        }

        // Fallback ke logo default di public
        $defaultLogo = $mode === 'dark' 
            ? 'storage/logo/logo-dark.jpeg' 
            : 'storage/logo/logo.jpeg';

        // Cek apakah file default ada
        if (file_exists(public_path($defaultLogo))) {
            return asset($defaultLogo);
        }

        // Ultimate fallback: gunakan logo light mode
        if ($mode === 'dark' && file_exists(public_path('storage/logo/logo.jpeg'))) {
            return asset('storage/logo/logo.jpeg');
        }

        // Jika benar-benar tidak ada logo sama sekali, return placeholder
        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="120" height="40" viewBox="0 0 120 40"%3E%3Crect width="120" height="40" fill="%23784828"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" fill="white" font-family="sans-serif" font-size="14" font-weight="bold"%3E' . rawurlencode($this->shop_name) . '%3C/text%3E%3C/svg%3E';
    }

    /**
     * Get favicon URL dengan fallback.
     */
    public function getFaviconUrl(): string
    {
        if (!empty($this->favicon)) {
            return asset('storage/' . $this->favicon);
        }

        // Fallback ke favicon default atau logo
        if (file_exists(public_path('storage/logo/favicon.png'))) {
            return asset('storage/logo/favicon.png');
        }

        // Jika tidak ada favicon, crop logo utama
        return $this->getLogoUrl('light');
    }
}
