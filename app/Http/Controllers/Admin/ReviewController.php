<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['product', 'user', 'images'])
            ->latest()
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        if (!$review->is_approved) {
            $review->is_approved = true;
            $review->save();
        }

        $product = $review->product;

        $approvedReviews = $product->reviews()
            ->where('is_approved', true)
            ->get();

        $product->review_count = $approvedReviews->count();
        $product->average_rating = $approvedReviews->avg('rating') ?? 0;

        $product->save();

        return back()->with('success', 'Review approved successfully.');
    }

    public function destroy(Review $review)
    {
        $product = $review->product;

        $review->delete();

        $product->review_count = $product->reviews()
            ->where('is_approved', true)
            ->count();

        $product->average_rating = $product->reviews()
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        $product->save();

        return back()->with('success', 'Review deleted successfully.');
    }

}
