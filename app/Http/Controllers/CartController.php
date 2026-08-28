<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $cart = $user->ensureCart();

        // Refresh harga dari produk terkini sebelum ditampilkan
        $cart->recalculateTotals();

        $cart->loadMissing(['items.product', 'items.product.images']);

        $itemsFormatted = $cart->items->map(function (CartItem $item) {
            $product = $item->product;
            $primaryImage = $product?->images?->firstWhere('is_primary', true)
                ?? $product?->images?->first();
            $imageUrl = null;
            if ($primaryImage?->url) {
                $imageUrl = (str_starts_with($primaryImage->url, 'http') || str_starts_with($primaryImage->url, '/'))
                    ? $primaryImage->url
                    : url('storage/' . ltrim($primaryImage->url, '/'));
            }

            return [
                'id' => $item->id,
                'cart_id' => $item->cart_id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal_price' => $item->subtotal_price,
                'product' => [
                    'id' => $product?->id,
                    'name' => $product?->name ?? '(Produk tidak ditemukan)',
                    'slug' => $product?->slug,
                    'stock_status' => $product?->stock_status ?? 'out_of_stock',
                    'image_url' => $imageUrl,
                ],
            ];
        })->values();

        return Inertia::render('Cart/Index', [
            'cart' => [
                'id' => $cart->id,
                'total_quantity' => $cart->total_quantity,
                'total_price' => $cart->total_price,
                'items' => $itemsFormatted,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|int|exists:products,id',
            'quantity' => 'nullable|int|min:1|max:100',
        ]);

        $qty = max(1, (int) ($validated['quantity'] ?? 1));

        /** @var \App\Models\Product $product */
        $product = Product::query()
            ->where('is_published', true)
            ->find($validated['product_id']);

        if (!$product) {
            throw ValidationException::withMessages([
                'product_id' => ['Produk tidak ditemukan atau belum diterbitkan.'],
            ]);
        }

        if ($product->stock_status === 'out_of_stock') {
            throw ValidationException::withMessages([
                'product_id' => ['Maaf, produk ini sedang habis (Sold Out).'],
            ]);
        }

        $user = $request->user();
        $cart = $user->ensureCart();

        DB::transaction(function () use ($cart, $product, $qty) {
            // Lock the cart row to prevent concurrent inserts of the same product
            $lockedCart = \App\Models\Cart::lockForUpdate()->find($cart->id);

            $existing = CartItem::query()
                ->where('cart_id', $lockedCart->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $existing->quantity = $existing->quantity + $qty;
                $existing->save();
            } else {
                CartItem::create([
                    'cart_id' => $lockedCart->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => (int) $product->price,
                    'subtotal_price' => (int) $product->price * $qty,
                ]);
            }

            $lockedCart->recalculateTotals();
        });

        return redirect()
            ->route('cart.index')
            ->with('cart.flash', sprintf(
                '✅ %s sebanyak %d item(s) telah ditambahkan ke keranjang!',
                $product->name,
                $qty
            ));
    }

    public function updateQuantity(Request $request, CartItem $cartItem): RedirectResponse
    {
        $user = $request->user();
        if ((int) $cartItem->cart->user_id !== (int) $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'quantity' => 'required|int|min:1|max:100',
        ]);

        DB::transaction(function () use ($cartItem, $validated) {
            CartItem::lockForUpdate()->find($cartItem->id);
            $cartItem->quantity = (int) $validated['quantity'];
            $cartItem->save();
        });

        return redirect()->route('cart.index');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $user = $request->user();
        if ((int) $cartItem->cart->user_id !== (int) $user->id) {
            abort(403, 'Unauthorized');
        }

        $cartItem->delete();

        return redirect()->route('cart.index');
    }

    public function clear(Request $request): RedirectResponse
    {
        $cart = $request->user()->ensureCart();
        $cart->items()->delete();
        $cart->recalculateTotals();

        return redirect()->route('cart.index');
    }
}
