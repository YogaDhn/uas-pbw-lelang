<?php

namespace App\Console\Commands;

use App\Models\Auction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Events\AuctionEnded;
#[Signature('app:close-auction-command')]
#[Description('Menutup auction yang sudah lewat waktu')]
class CloseAuctionCommand extends Command
{
    public function handle()
{
    $auctions = Auction::where('status', 'active')
        ->where('end_time', '<=', now())
        ->get();

    foreach ($auctions as $auction) {

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

        // Ambil ulang bid beserta user
        $highestBid = $highestBid->fresh()->load('user');
    }

    $auction->update([
        'status' => 'ended'
    ]);

    // Ambil ulang auction
    $auction = $auction->fresh();
\Log::info('MENGIRIM AUCTION ENDED', [
    'auction_id' => $auction->id,
    'winner' => $highestBid?->user?->name,
]);
    event(new AuctionEnded(
        $auction,
        $highestBid
    ));
}

    $this->info('Auction selesai diperiksa.');
}
}