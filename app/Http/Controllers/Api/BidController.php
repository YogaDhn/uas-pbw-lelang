<?php

namespace App\Http\Controllers\Api;

use App\Models\Bid;
use App\Models\Auction;
use App\Events\BidPlaced;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\OutbidNotification;
use Illuminate\Support\Facades\DB;
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

    $oldHighestBid = $highestBid;

    if($auction->status != 'active')
    {
        return response()->json([
            'message' => 'Auction tidak aktif'
        ],403);
    }

    if($auction->user_id == auth()->id())
    {
        return response()->json([
            'message' => 'Tidak boleh menawar lelang sendiri'
        ],403);
    }

    $minimumBid = $highestBid
        ? $highestBid->amount + $auction->bid_increment
        : $auction->starting_price;

    if ($request->amount < $minimumBid)
    {
        return response()->json([
            'message' => 'Bid terlalu rendah',
            'minimum_bid' => $minimumBid
        ], 422);
    }

    try {

    $bid = DB::transaction(function () use (
        $auction,
        $request
    ) {

        $highestBid = $auction->bids()
            ->lockForUpdate()
            ->orderByDesc('amount')
            ->first();

        $minimumBid = $highestBid
            ? $highestBid->amount + $auction->bid_increment
            : $auction->starting_price;

        if ($request->amount < $minimumBid) {
            throw new \Exception(
                'Bid terlalu rendah'
            );
        }

        return Bid::create([
            'auction_id' => $auction->id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'is_winner' => false
        ]);
    });

} catch (\Exception $e) {

    return response()->json([
        'message' => $e->getMessage()
    ], 422);

}

    event(new BidPlaced($bid));

    if (
        $oldHighestBid &&
        $oldHighestBid->user_id != auth()->id()
    )
    {
        event(
            new OutbidNotification(
                $oldHighestBid,
                $bid
            )
        );
    }

    \Log::info('BidPlaced Event Fired', [
        'bid_id' => $bid->id,
        'auction_id' => $auction->id,
        'amount' => $bid->amount
    ]);

    return response()->json([
        'message' => 'Bid berhasil',
        'data' => $bid
    ]);
}}