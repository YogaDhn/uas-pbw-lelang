<?php

namespace App\Models;
use App\Models\User;
use App\Models\Bid;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = [
    'user_id',
    'title',
    'description',
    'image',
    'starting_price',
    'bid_increment',
    'start_time',
    'end_time',
    'status'
];
public function user()
{
    return $this->belongsTo(User::class);
}

public function bids()
{
    return $this->hasMany(Bid::class);
}}
