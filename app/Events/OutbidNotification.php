<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OutbidNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Bid $oldBid;
    public Bid $newBid;

    public function __construct(
    Bid $oldBid,
    Bid $newBid
)
{
    \Log::info('OUTBID EVENT FIRED');

    $this->oldBid = $oldBid;
    $this->newBid = $newBid;
}

    public function broadcastOn(): array
    {
        return [
            new Channel(
                'user.' . $this->oldBid->user_id
            )
        ];
    }

    public function broadcastAs(): string
    {
        return 'outbid.notification';
    }
}