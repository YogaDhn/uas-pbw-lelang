<?php

namespace App\Console\Commands;

use App\Models\Auction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

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
            $highestBid = $auction->bids()
                ->orderByDesc('amount')
                ->first();

            if ($highestBid)
{
    $auction->bids()->update([
        'is_winner' => false
    ]);

    $highestBid->update([
        'is_winner' => true
    ]);
}

            $auction->update([
                'status' => 'ended'
            ]);
        }

        $this->info(
            'Auction selesai diperiksa.'
        );
    }
}