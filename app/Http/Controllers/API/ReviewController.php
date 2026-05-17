<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('user');

        if ($request->has('listing_id')) {
            $query->where('listing_id', $request->listing_id);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'rating'     => 'required|integer|between:1,5',
            'comment'    => 'nullable|string',
        ]);

        // Prevent duplicate reviews
        $existing = Review::where('user_id', $request->user()->id)
            ->where('listing_id', $request->listing_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this listing'
            ], 422);
        }

        $review = Review::create([
            'id'         => Str::uuid(),
            'user_id'    => $request->user()->id,
            'listing_id' => $request->listing_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'source'     => 'user',
        ]);

        return response()->json(['success' => true, 'data' => $review], 201);
    }

    public function show($id)
    {
        $review = Review::with('user')->find($id);

        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $review]);
    }

    public function update(Request $request, $id)
    {
        $review = Review::where('user_id', $request->user()->id)->find($id);

        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $review->update($request->only(['rating', 'comment']));

        return response()->json(['success' => true, 'data' => $review]);
    }

    public function destroy(Request $request, $id)
    {
        $review = Review::where('user_id', $request->user()->id)->find($id);

        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $review->delete();

        return response()->json(['success' => true, 'message' => 'Review deleted']);
    }
}