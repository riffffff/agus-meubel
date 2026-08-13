<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @mixin Builder
 */
class ShopSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
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

        return parent::save($options);
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

    public static function getSettings(): self
    {
        $settings = static::query()->find(self::SINGLETON_ID);

        if ($settings === null) {
            $settings = new static();
            $settings->forceFill([
                'id' => self::SINGLETON_ID,
                'shop_name' => 'Agus Mebel Jepara',
                'address' => 'Jepara, Jawa Tengah, Indonesia',
                'whatsapp_number' => '6281234567890',
                'whatsapp_template' => 'Halo, saya tertarik dengan produk *{product_name}* seharga {product_price}. Apakah tersedia?',
                'operating_hours' => 'Senin - Sabtu: 08:00 - 17:00',
                'hero_banner_text_1' => 'Furniture Kayu Jati Premium',
                'hero_banner_text_2' => 'Kualitas Terbaik Langsung dari Pengrajin Jepara',
                'hero_banner_bg' => null,
                'shipping_areas' => ['Seluruh Indonesia'],
                'shipping_estimate_days' => '7 - 14 hari kerja',
            ]);
            $settings->save();
        }

        return $settings;
    }
}
