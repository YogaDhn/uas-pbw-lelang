<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index()
    {
        return Auction::with('user')
                      ->latest()
                      ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'starting_price' => 'required|numeric',
            'bid_increment' => 'required|numeric',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        $auction = Auction::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'starting_price' => $request->starting_price,
            'bid_increment' => $request->bid_increment,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'scheduled'
        ]);

        return response()->json($auction, 201);
    }

    public function show(Auction $auction)
    {
        return $auction->load([
            'user',
            'bids'
        ]);
    }

  public function closeAuction(Auction $auction)
{
    $highestBid = $auction->bids()
        ->orderByDesc('amount')
        ->first();

    if($highestBid)
    {
        $highestBid->update([
            'is_winner' => true
        ]);
    }

    $auction->update([
    'status' => 'ended'
]);

    return response()->json([
        'message' => 'Auction closed',
        'winner' => $highestBid
    ]);
}

    public function update(Request $request, Auction $auction)
    {
        // Otorisasi pemilik
        if (auth()->id() != $auction->user_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Hanya boleh saat scheduled
        if ($auction->status != 'scheduled') {
            return response()->json([
                'message' => 'Auction sudah berjalan'
            ], 403);
        }

        $auction->update($request->all());

        return response()->json($auction);
    }

    public function destroy(Auction $auction)
    {
        // Otorisasi pemilik
        if (auth()->id() != $auction->user_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Hanya boleh saat scheduled
        if ($auction->status != 'scheduled') {
            return response()->json([
                'message' => 'Auction sudah berjalan'
            ], 403);
        }

        $auction->delete();

        return response()->json([
            'message' => 'Auction berhasil dihapus'
        ]);
    }

    public function winner(Auction $auction)
{
    $winnerBid = $auction->bids()
        ->with('user')
        ->where('is_winner', true)
        ->first();

    if (!$winnerBid) {
        return response()->json([
            'message' => 'Belum ada pemenang'
        ]);
    }

    return response()->json([
        'auction' => $auction->title,
        'winner' => $winnerBid->user->name,
        'winning_bid' => $winnerBid->amount
    ]);
}
}