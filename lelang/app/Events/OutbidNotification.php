<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OutbidNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Bid $oldBid;
    public Bid $newBid;

   public function __construct(
    Bid $oldBid,
    Bid $newBid
) {
    \Log::info('OUTBID CONSTRUCTOR');

    $this->oldBid = $oldBid;
    $this->newBid = $newBid;
}

    public function broadcastOn(): array
{
    return [
        new Channel(
            'auction.' . $this->oldBid->auction_id
        )
    ];
}

    public function broadcastAs(): string
    {
        return 'OutbidNotification';
    }

    public function broadcastWith(): array
    {
            \Log::info('OUTBID BROADCAST');
        return [
            'message' => 'Kamu sudah dikalahkan bid lain!',
            'old_user_id' => $this->oldBid->user_id,
            'new_user_id' => $this->newBid->user_id,
            'auction_id' => $this->oldBid->auction_id,
        ];
    }
}