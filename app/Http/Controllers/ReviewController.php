<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();

        $hasPurchased = OrderItem::where('product_id', $validated['product_id'])
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'delivered');
            })
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        $review = Review::updateOrCreate(
            [
                'product_id' => $validated['product_id'],
                'user_id' => $user->id,
                'order_id' => $validated['order_id'],
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
                'reviewer_name' => $user->name,
                'is_verified_purchase' => true,
            ]
        );

        // 🔥 IMPORTANT: update product rating AFTER review change
        $review->product->updateRatingStats();

        return back()->with(
            'success',
            $review->wasRecentlyCreated
            ? 'Review submitted successfully.'
            : 'Review updated successfully.'
        );
    }
}
