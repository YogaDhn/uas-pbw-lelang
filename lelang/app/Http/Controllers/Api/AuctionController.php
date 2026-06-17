<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAuctionRequest;
use App\Http\Requests\UpdateAuctionRequest;
use App\Events\AuctionEnded;
use Carbon\Carbon;
class AuctionController extends Controller
{
    public function index()
    {
        return Auction::with('user')
                      ->latest()
                      ->get();
    }

public function store(StoreAuctionRequest $request)
{
    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')
            ->store('auctions', 'public');
    }

    
    $auction = Auction::create([
        'user_id' => auth()->id(),
        'title' => $request->title,
        'description' => $request->description,
        'image' => $imagePath,
        'starting_price' => $request->starting_price,
        'bid_increment' => $request->bid_increment,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'status' => now()->between(
            $request->start_time,
            $request->end_time
        ) ? 'active' : 'scheduled'
    ]);

    return response()->json($auction, 201);
}

   public function show(Auction $auction)
{
    $auction->updateStatus();
    $auction->load([
        'user',
        'bids.user'
    ]);

    $highestBid = $auction->bids()
        ->with('user')
        ->orderByDesc('amount')
        ->first();

    return response()->json([
        'id' => $auction->id,
        'title' => $auction->title,
        'description' => $auction->description,
        'starting_price' => $auction->starting_price,
        'bid_increment' => $auction->bid_increment,
        'status' => $auction->status,
        'end_time' => $auction->end_time,
        'bids' => $auction->bids()
    ->orderByDesc('amount')
    ->get(),
        'user_id' => $auction->user_id,

        'winner' => $auction->status === 'ended'
            ? ($highestBid?->user?->name)
            : null,

        'winning_bid' => $auction->status === 'ended'
            ? ($highestBid?->amount)
            : null,
    ]);
}

public function myAuctions()
{
    return Auction::where(
        'user_id',
        auth()->id()
    )
    ->latest()
    ->get();
}

  public function closeAuction(Auction $auction)
{
    $auction->bids()->update([
        'is_winner' => false
    ]);

    $highestBid = $auction->bids()
        ->orderByDesc('amount')
        ->first();

    if ($highestBid) {
        $highestBid->update([
            'is_winner' => true
        ]);
    }

    $auction->update([
        'status' => 'ended'
    ]);
    event(new AuctionEnded($auction));

    return response()->json([
        'message' => 'Auction closed',
        'winner' => $highestBid
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
    public function update(
    UpdateAuctionRequest $request,
    Auction $auction
)
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

        $auction->update($request->validated());

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
}