<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ListingController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\ReviewController;

// Public listing routes
Route::get('/listings', [ListingController::class, 'index']);
Route::get('/listings/{id}', [ListingController::class, 'show']);

// Category shortcuts
Route::get('/chalets',     fn() => app(ListingController::class)->index(request()->merge(['category' => 'chalet'])));
Route::get('/eateries',    fn() => app(ListingController::class)->index(request()->merge(['category' => 'eatery'])));
Route::get('/attractions', fn() => app(ListingController::class)->index(request()->merge(['category' => 'attraction'])));
Route::get('/mosques',     fn() => app(ListingController::class)->index(request()->merge(['category' => 'mosque'])));

// Public review reading
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{id}', [ReviewController::class, 'show']);

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected routes (must be logged in)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('reviews',  ReviewController::class)->except(['index', 'show']);
    Route::apiResource('listings', ListingController::class)->except(['index', 'show']);
});

// Get current user's roles
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me/roles', function (Request $request) {
        $roles = \App\Models\UserRole::where('user_id', $request->user()->id)->pluck('role');
        return response()->json(['roles' => $roles]);
    });

    Route::post('/me/become-owner', function (Request $request) {
        \App\Models\UserRole::firstOrCreate([
            'user_id' => $request->user()->id,
            'role'    => 'business_owner',
        ], ['id' => (string) \Illuminate\Support\Str::uuid()]);
        return response()->json(['success' => true]);
    });
});