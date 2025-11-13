<?php

use App\Http\Controllers\Api\TenderController;
use Illuminate\Support\Facades\Route;

Route::get('/tenders', [TenderController::class, 'index']);
Route::get('/tenders/{tender}', [TenderController::class, 'show']);
Route::post('/tenders', [TenderController::class, 'store']);
