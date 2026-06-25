<?php

namespace App\Events;

use App\Models\Auction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Auction $auction;
    public $winner;

    public function __construct(
        Auction $auction,
        $winner
    )
    {
        $this->auction = $auction;
        $this->winner = $winner;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel(
                'auction.' . $this->auction->id
            )
        ];
    }

    public function broadcastAs(): string
    {
        return 'auction.ended';
    }

public function broadcastWith(): array
{
    \Log::info('BROADCAST AUCTION ENDED');

    return [
        'auction_id' => $this->auction->id,
        'winner' => $this->winner
            ? $this->winner->user->name
            : 'Tidak ada pemenang',
        'winning_bid' => $this->winner
            ? $this->winner->amount
            : 0,
    ];
}
}