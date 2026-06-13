<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel(
    'auction.{auctionId}',
    function ($user, $auctionId) {
        return true;
    }
);