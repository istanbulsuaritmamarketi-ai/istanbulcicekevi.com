<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('product');

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();
        $pendingCount = Review::where('is_approved', false)->count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount'));
    }

    public function toggleApproval(Review $review)
    {
        $review->update([
            'is_approved' => !$review->is_approved
        ]);

        $status = $review->is_approved ? 'onaylandı ve yayına alındı' : 'onayı kaldırıldı';
        return back()->with('success', "Yorum {$status}.");
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Yorum silindi.');
    }
}
