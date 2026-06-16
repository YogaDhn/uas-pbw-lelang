<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuctionController;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
use App\Http\Controllers\Api\BidController;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource(
        'auctions',
        AuctionController::class
    );

    Route::post(
    '/auctions/{auction}/close',
    [AuctionController::class,'closeAuction']
);

    Route::post(
        '/auctions/{auction}/bid',
        [BidController::class, 'store']
    );
    
    Route::post(
        '/auctions/{auction}/bids',
        [BidController::class, 'store']
    );

    Route::get(
        '/auctions/{auction}/bids',
        [BidController::class, 'history']
    );
    Route::get(
    '/auctions/{auction}/winner',
    [AuctionController::class, 'winner']
);
});