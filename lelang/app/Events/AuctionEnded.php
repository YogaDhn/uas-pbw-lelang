<?php

namespace App\Events;

use App\Models\Auction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionEnded implements ShouldBroadcast
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
        return [
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'winner' => $this->winner
                ? $this->winner->user->name
                : null,
            'winning_bid' => $this->winner
                ? $this->winner->amount
                : null
        ];
    }
}