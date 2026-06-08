<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Reviews/Form', [
            'isEdit' => false,
            'review' => null
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);

        Review::create([
            'name' => $request->name,
            'city' => $request->city,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan berhasil dibuat.');
    }

    public function edit(Review $review)
    {
        return Inertia::render('Admin/Reviews/Form', [
            'isEdit' => true,
            'review' => $review
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);

        $review->update([
            'name' => $request->name,
            'city' => $request->city,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan berhasil diperbarui.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan berhasil dihapus.');
    }
}
