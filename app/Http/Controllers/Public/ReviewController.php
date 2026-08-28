<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Simpan review baru dari halaman publik.
     *
     * Proteksi yang diterapkan:
     * - Rate limiting via throttle:review middleware (di routes/web.php)
     * - Honeypot field (website) harus kosong
     * - Produk harus published
     * - Review default is_approved = false → perlu moderasi admin
     * - Satu IP maksimal 3 review per 24 jam (via rate limiter 'review')
     */
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: bot biasanya mengisi semua field, termasuk field tersembunyi
        if ($request->filled('website')) {
            // Kembalikan response sukses palsu agar bot tidak tahu bahwa terdeteksi
            return back()->with('review_submitted', true);
        }

        $validated = $request->validate([
            'product_id'   => ['required', 'integer', 'exists:products,id'],
            'name'         => ['required', 'string', 'min:2', 'max:100'],
            'city'         => ['nullable', 'string', 'max:100'],
            'rating'       => ['required', 'integer', 'min:1', 'max:5'],
            'review'       => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        // Pastikan produk published
        $product = Product::where('id', $validated['product_id'])
            ->where('is_published', true)
            ->first();

        if (! $product) {
            throw ValidationException::withMessages([
                'product_id' => ['Produk tidak ditemukan.'],
            ]);
        }

        Review::create([
            'product_id'  => $validated['product_id'],
            'name'        => $validated['name'],
            'city'        => $validated['city'] ?? null,
            'rating'      => (int) $validated['rating'],
            'review'      => $validated['review'],
            'is_approved' => false, // Selalu menunggu moderasi admin
        ]);

        return back()->with('review_submitted', true);
    }
}
