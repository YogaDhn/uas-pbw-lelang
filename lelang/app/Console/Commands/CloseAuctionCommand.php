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
    \Log::info('COMMAND RUNNING');

    $auctions = Auction::where('status', 'active')
        ->where('end_time', '<=', now())
        ->get();

    \Log::info('AUCTION FOUND', [
        'count' => $auctions->count()
    ]);

    foreach ($auctions as $auction)
    {
        \Log::info('CHECK AUCTION', [
            'auction_id' => $auction->id
        ]);

        $highestBid = $auction->bids()
            ->with('user')
            ->orderBy('amount', 'desc')
            ->first();

        \Log::info('HIGHEST BID CHECK', [
            'auction_id' => $auction->id,
            'bid_exists' => $highestBid ? true : false
        ]);

        if ($highestBid)
        {
            $highestBid->load('user');

            \Log::info('WINNER DATA', [
                'auction_id' => $auction->id,
                'user' => $highestBid->user->name ?? null,
                'amount' => $highestBid->amount ?? null
            ]);

            $auction->bids()->update([
                'is_winner' => false
            ]);

            $highestBid->update([
                'is_winner' => true
            ]);

            event(new AuctionEnded($auction, $highestBid));
        }

        $auction->update([
            'status' => 'ended'
        ]);

        \Log::info('AUCTION ENDED EVENT FIRED', [
            'auction_id' => $auction->id
        ]);
    }

    $this->info('Auction selesai diperiksa.');
}
}