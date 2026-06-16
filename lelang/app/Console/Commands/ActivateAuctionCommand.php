<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Auction;
#[Signature('app:activate-auction-command')]
#[Description('Command description')]
class ActivateAuctionCommand extends Command
{
    /**
     * Execute the console command.
     */
   public function handle()
{
    $auctions = Auction::where(
        'status',
        'scheduled'
    )
    ->where(
        'start_time',
        '<=',
        now()
    )
    ->get();

    foreach ($auctions as $auction)
    {
        $auction->update([
            'status' => 'active'
        ]);
    }

    $this->info(
        'Auction aktif diperiksa.'
    );
}
}
