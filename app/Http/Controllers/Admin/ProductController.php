<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $products = Product::with('images')->orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Products/Index', [
            'products' => $products
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Form', [
            'isEdit' => false,
            'product' => null
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'stock_status' => 'required|in:ready_stock,pre_order',
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:5120', // max 5MB per image
            'primary_image_index' => 'nullable|integer',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => $request->price,
            'stock_status' => $request->stock_status,
        ]);

        $primaryIndex = $request->input('primary_image_index', 0);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $imageFile) {
                $imagePath = $this->imageService->process($imageFile, 'products');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $imagePath,
                    'is_primary' => $index == $primaryIndex,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product)
    {
        $product->load('images');
        return Inertia::render('Admin/Products/Form', [
            'isEdit' => true,
            'product' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'stock_status' => 'required|in:ready_stock,pre_order',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'deleted_image_ids' => 'nullable|array',
            'deleted_image_ids.*' => 'integer',
            'primary_image_id' => 'nullable|integer', // if selecting an existing image
            'primary_image_index' => 'nullable|integer', // if selecting one of the newly uploaded images
        ]);

        // 1. Update product base info
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => $request->price,
            'stock_status' => $request->stock_status,
        ]);

        // 2. Delete selected images
        if ($request->filled('deleted_image_ids')) {
            $imagesToDelete = ProductImage::whereIn('id', $request->deleted_image_ids)
                ->where('product_id', $product->id)
                ->get();

            foreach ($imagesToDelete as $img) {
                Storage::disk('public')->delete($img->url);
                $img->delete();
            }
        }

        // 3. Process new images if uploaded
        $newPrimaryIndex = $request->input('primary_image_index'); // index of the new image to be primary
        $hasNewImages = $request->hasFile('images');

        if ($hasNewImages) {
            foreach ($request->file('images') as $index => $imageFile) {
                $imagePath = $this->imageService->process($imageFile, 'products');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $imagePath,
                    // If we specified a new image index as primary, set it
                    'is_primary' => ($newPrimaryIndex !== null && $index == $newPrimaryIndex),
                ]);
            }
        }

        // 4. Update primary image settings
        $primaryImageId = $request->input('primary_image_id');

        if ($primaryImageId) {
            // Set the selected existing image as primary, set all others as false
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            ProductImage::where('id', $primaryImageId)->where('product_id', $product->id)->update(['is_primary' => true]);
        } elseif ($newPrimaryIndex !== null && $hasNewImages) {
            // Already set in the loop above, but let's make sure all others are false
            $primaryImage = ProductImage::where('product_id', $product->id)
                ->where('is_primary', true)
                ->first();
                
            if ($primaryImage) {
                ProductImage::where('product_id', $product->id)
                    ->where('id', '!=', $primaryImage->id)
                    ->update(['is_primary' => false]);
            }
        }

        // Make sure there is at least one primary image if any images exist
        $primaryExists = ProductImage::where('product_id', $product->id)->where('is_primary', true)->exists();
        if (!$primaryExists) {
            $firstImage = ProductImage::where('product_id', $product->id)->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Load and delete all images from storage
        $images = $product->images;
        foreach ($images as $img) {
            Storage::disk('public')->delete($img->url);
            $img->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
