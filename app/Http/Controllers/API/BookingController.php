<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['listing', 'roomType'])
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'listing_id'  => 'required|exists:listings,id',
            'check_in'    => 'required|date',
            'check_out'   => 'nullable|date|after:check_in',
            'guests'      => 'required|integer|min:1',
            'total_price' => 'required|numeric',
        ]);

        $booking = Booking::create([
            ...$request->all(),
            'id'      => Str::uuid(),
            'user_id' => $request->user()->id,
            'status'  => 'upcoming',
        ]);

        return response()->json(['success' => true, 'data' => $booking], 201);
    }

    public function show(Request $request, $id)
    {
        $booking = Booking::with(['listing', 'roomType'])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $booking]);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::where('user_id', $request->user()->id)->find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $booking->update($request->only(['status', 'special_requests']));

        return response()->json(['success' => true, 'data' => $booking]);
    }

    public function destroy(Request $request, $id)
    {
        $booking = Booking::where('user_id', $request->user()->id)->find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $booking->delete();

        return response()->json(['success' => true, 'message' => 'Booking cancelled']);
    }
}