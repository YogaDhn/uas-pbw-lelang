<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starting_price' => 'required|numeric|min:1',
            'bid_increment' => 'required|numeric|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ];
    }
}