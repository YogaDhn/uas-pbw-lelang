<?php

namespace App\Console\Commands;

use App\Models\Auction;
use Carbon\Carbon;
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
    $auctions = Auction::where('status', '!=', 'ended')->get();

    foreach ($auctions as $auction) {

        if (
            $auction->start_time <= now() &&
            $auction->end_time > now()
        ) {
            $auction->status = 'active';
        }

        if ($auction->end_time <= now()) {
            $auction->status = 'ended';
        }

        $auction->save();
    }

    $this->info('Status auction berhasil diupdate');
}
}


