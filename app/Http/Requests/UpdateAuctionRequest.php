<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'starting_price' => 'sometimes|numeric|min:1',
            'bid_increment' => 'sometimes|numeric|min:1',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date'
        ];
    }
}