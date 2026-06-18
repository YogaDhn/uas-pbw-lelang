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

    try {

        $bid = DB::transaction(function () use (
            $request,
            $auction
        ) {

            $auction = Auction::lockForUpdate()
                ->find($auction->id);

            if ($auction->status != 'active') {
                throw new \Exception(
                    'Auction tidak aktif'
                );
            }

            if ($auction->user_id == auth()->id()) {
                throw new \Exception(
                    'Tidak boleh menawar lelang sendiri'
                );
            }

            $highestBid = $auction->bids()
                ->orderByDesc('amount')
                ->first();

            $oldHighestBid = $highestBid;

            $minimumBid = $highestBid
                ? $highestBid->amount + $auction->bid_increment
                : $auction->starting_price;

            if ($request->amount < $minimumBid) {
                throw new \Exception(
                    'Bid terlalu rendah'
                );
            }

            // Buat bid baru
            $bid = Bid::create([
                'auction_id' => $auction->id,
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'is_winner' => false
            ]);

            // Broadcast realtime bid
            event(new BidPlaced($bid));

            // Kirim notifikasi ke bidder sebelumnya
            if (
                $oldHighestBid &&
                $oldHighestBid->user_id != auth()->id()
            ) {
                event(
                    new OutbidNotification(
                        $oldHighestBid,
                        $bid
                    )
                );
            }

            return $bid;
        });

        return response()->json([
            'message' => 'Bid berhasil',
            'data' => $bid
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'message' => $e->getMessage()
        ], 422);

    }
}}