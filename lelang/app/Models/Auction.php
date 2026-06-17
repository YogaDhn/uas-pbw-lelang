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

public function updateStatus()
{
    if ($this->start_time <= now() && $this->end_time > now()) {
        $this->status = 'active';
    }

    if ($this->end_time <= now()) {
        $this->status = 'ended';
    }

    $this->save();
}

public function getStatusAttribute($value)
{
    if ($this->start_time <= now() && $this->end_time > now()) {
        return 'active';
    }

    if ($this->end_time <= now()) {
        return 'ended';
    }

    return 'scheduled';
}

protected $casts = [
    'end_time' => 'datetime',
    'start_time' => 'datetime',
];
public function bids()
{
    return $this->hasMany(Bid::class);
}}
