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
           'title' => 'required',
        'description' => 'required',
        'starting_price' => 'required|numeric',
        'bid_increment' => 'required|numeric',
        'start_time' => 'required',
        'end_time' => 'required'
        ];
    }
}