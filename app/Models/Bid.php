<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Auction;

class Bid extends Model
{
    protected $fillable = [
        'auction_id',
        'user_id',
        'amount',
        'is_winner'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
}