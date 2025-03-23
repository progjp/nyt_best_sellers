<?php

use App\Http\Controllers\BestSellersNYTController;
use Illuminate\Support\Facades\Route;

Route::get('/api/v1/nyt-bestsellers', [BestSellersNYTController::class, 'index']);
