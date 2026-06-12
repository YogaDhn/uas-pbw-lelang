<?php

namespace App\Http\Controllers\Api;

use App\Models\Bid;
use App\Models\Auction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BidController extends Controller
{
    public function store(
        Request $request,
        Auction $auction
    )
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $highestBid = $auction->bids()
            ->orderByDesc('amount')
            ->first();

        $minimumBid = $highestBid
            ? $highestBid->amount + $auction->bid_increment
            : $auction->starting_price;

        if ($request->amount < $minimumBid) {

            return response()->json([
                'message' => 'Bid terlalu rendah',
                'minimum_bid' => $minimumBid
            ], 422);
        }

        $bid = Bid::create([
            'auction_id' => $auction->id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'is_winner' => false
        ]);

        return response()->json([
            'message' => 'Bid berhasil',
            'data' => $bid
        ]);
    }

    public function history(
        Auction $auction
    )
    {
        return $auction->bids()
            ->with('user')
            ->orderByDesc('amount')
            ->get();
    }
}