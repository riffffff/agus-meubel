<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo PHP_EOL . '===== Test 1: ShopSetting Save =====' . PHP_EOL;
$s = \App\Models\ShopSetting::getSettings();
echo 'ID: ' . $s->id . PHP_EOL;
echo 'Nama Toko Saat Ini: ' . $s->shop_name . PHP_EOL;
$originalName = $s->shop_name;
$s->shop_name = 'PHPUnit Test Name';
$ok = $s->save();
echo 'Save Result: ' . ($ok ? 'OK' : 'FAIL') . PHP_EOL;
$s2 = \App\Models\ShopSetting::getSettings();
echo 'Nama Toko Setelah Save: ' . $s2->shop_name . PHP_EOL;
echo 'Jumlah Row: ' . \App\Models\ShopSetting::count() . PHP_EOL;
if ($s2->shop_name === 'PHPUnit Test Name' && \App\Models\ShopSetting::count() === 1) {
    echo "✅ ShopSetting SINGLETON BERHASIL" . PHP_EOL;
} else {
    echo "❌ ShopSetting GAGAL" . PHP_EOL;
}
$s2->shop_name = $originalName;
$s2->save();
echo '(Nama dikembalikan ke asli: ' . $s2->shop_name . ')' . PHP_EOL;

echo PHP_EOL . '===== Test 2: ProductImage Foreign Key Insert =====' . PHP_EOL;
$p = \App\Models\Product::first();
if (!$p) {
    echo "⚠️ Skip: Tidak ada product di DB" . PHP_EOL;
} else {
    echo 'Product ID: ' . $p->id . ' - ' . $p->name . PHP_EOL;
    echo 'Sebelum: ' . $p->images()->count() . ' gambar' . PHP_EOL;
    try {
        $new = \App\Models\ProductImage::create([
            'product_id' => $p->id,
            'url' => 'test_fk_' . time() . '.webp',
            'is_primary' => false,
            'sort_order' => 999,
        ]);
        echo 'INSERT Success -> Image ID: ' . $new->id . PHP_EOL;
        $new->delete();
        echo "✅ ProductImage FK BERHASIL (Insert + Delete lancar)" . PHP_EOL;
    } catch (\Throwable $e) {
        echo '❌ FK ERROR: ' . $e::class . PHP_EOL;
        echo '   Msg: ' . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "===== SEMUA TEST SELESAI =====" . PHP_EOL;
