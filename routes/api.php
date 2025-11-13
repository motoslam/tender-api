<?php

use App\Http\Controllers\Api\TenderController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/tenders', [TenderController::class, 'index']);
Route::get('/tenders/{tender}', [TenderController::class, 'show']);

// предположу, что только Post запрос нужно закрыть авторизацией, остальные методы публичные
Route::post('/tenders', [TenderController::class, 'store'])->middleware('auth:sanctum');
