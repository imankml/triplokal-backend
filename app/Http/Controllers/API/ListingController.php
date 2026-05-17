<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    // GET /api/listings?category=chalet
    public function index(Request $request)
    {
        $query = Listing::where('is_active', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by logged-in owner for business dashboard
        if ($request->has('mine') && $request->user()) {
            $query->where('owner_id', $request->user()->id);
        }

        $listings = $query->get()->map(function ($listing) {
            $data = $listing->toArray();
            $data['avg_rating'] = $listing->reviews_avg_rating
                ? round($listing->reviews_avg_rating, 1)
                : null;
            $data['review_count'] = $listing->reviews_count ?? 0;
            return $data;
        });

        return response()->json(['success' => true, 'data' => $listings]);
    }

    // GET /api/listings/{id}
    public function show($id)
    {
        $listing = Listing::with([
            'reviews',
            'roomTypes' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }
        ])->find($id);

        if (!$listing) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $reviews = $listing->reviews;
        $avgRating = $reviews->count() > 0
            ? round($reviews->avg('rating'), 1)
            : null;

        $data = $listing->toArray();
        $data['avg_rating'] = $avgRating;
        $data['review_count'] = $reviews->count();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|in:chalet,eatery,attraction,mosque',
            'name'     => 'required|string',
            'image'    => 'nullable|image|max:5120',
        ]);

        $images = [];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('listings', 'public');
            $images = ['/storage/' . $path];
        }

        $listing = Listing::create([
            ...$request->except('image'),
            'id'     => (string) Str::uuid(),
            'images' => $images,
        ]);

        return response()->json(['success' => true, 'data' => $listing], 201);
    }

    public function update(Request $request, $id)
    {
        $listing = Listing::find($id);
        if (!$listing) return response()->json(['success' => false], 404);

        $listing->update($request->all());
        return response()->json(['success' => true, 'data' => $listing]);
    }

    public function destroy($id)
    {
        $listing = Listing::find($id);
        if (!$listing) return response()->json(['success' => false], 404);

        $listing->delete();
        return response()->json(['success' => true]);
    }
}