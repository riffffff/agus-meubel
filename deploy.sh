#!/bin/bash
# ============================================================
# Script Persiapan Deploy ke Shared Hosting
# Usage: bash deploy.sh
# ============================================================

set -e

echo "======================================"
echo " Agus Mebel — Persiapan Deploy"
echo "======================================"

# 1. Build frontend assets
echo ""
echo "[1/5] Build frontend assets..."
npm run build

# 2. Install composer dependencies (no dev)
echo ""
echo "[2/5] Install Composer dependencies (no dev)..."
composer install --optimize-autoloader --no-dev --no-interaction

# 3. Bersihkan file yang tidak perlu di-upload
echo ""
echo "[3/5] Membuat arsip untuk upload..."

# File/folder yang TIDAK perlu diupload ke hosting:
EXCLUDES=(
    ".git"
    ".gitignore"
    ".gitattributes"
    "node_modules"
    ".env"
    ".env.example"
    ".env.production"
    "tests"
    "deploy.sh"
    "*.sh"
    "agent"
    ".editorconfig"
    ".npmrc"
    "vite.config.js"
    "tailwind.config.js"
    "package.json"
    "package-lock.json"
    "tsconfig.json"
    "resources/js"
    "resources/css"
    "phpunit.xml"
    "bootstrap/ssr"
)

# Buat exclude flags untuk tar
EXCLUDE_FLAGS=""
for item in "${EXCLUDES[@]}"; do
    EXCLUDE_FLAGS="$EXCLUDE_FLAGS --exclude=./$item"
done

tar -czf ../agusmeubel-deploy.tar.gz $EXCLUDE_FLAGS -C /home/rifai/project/agus-meubel .

echo ""
echo "[4/5] Selesai! File: ../agusmeubel-deploy.tar.gz"
echo ""
echo "======================================"
echo " LANGKAH SELANJUTNYA:"
echo "======================================"
echo ""
echo "1. Login ke cPanel Arenhost"
echo "2. Buat database MySQL + user di 'MySQL Databases'"
echo "3. Upload agusmeubel-deploy.tar.gz via File Manager"
echo "   ke folder LUAR public_html (misal: /home/username/)"
echo "4. Extract di sana"
echo "5. PINDAHKAN isi folder public/ ke dalam public_html/"
echo "6. Edit public_html/index.php — ubah path:"
echo "   require __DIR__.'/../laravel/vendor/autoload.php';"
echo "   (sesuaikan dengan struktur folder kamu)"
echo "7. Copy .env.production ke .env, isi DB credentials"
echo "8. Jalankan via SSH atau PHP Manager:"
echo "   php artisan key:generate"
echo "   php artisan migrate --force"
echo "   php artisan storage:link"
echo "   php artisan optimize"
echo ""
echo "[5/5] Done."
