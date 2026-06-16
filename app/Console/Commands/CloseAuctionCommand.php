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
        $auctions = Auction::where(
            'status',
            'active'
        )
        ->where(
            'end_time',
            '<=',
            now()
        )
        ->get();

        foreach ($auctions as $auction)
{
    $auction->bids()->update([
        'is_winner' => false
    ]);

    $highestBid = $auction->bids()
        ->orderByDesc('amount')
        ->first();

    if ($highestBid)
    {
        $highestBid->update([
            'is_winner' => true
        ]);
    }

    $auction->update([
        'status' => 'ended'
    ]);
    event(new AuctionEnded($auction));
}

        $this->info(
            'Auction selesai diperiksa.'
        );
    }
}