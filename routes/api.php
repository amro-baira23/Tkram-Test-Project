<?php

use App\Http\Controllers\AuthController;
use Illuminate\Routing\Route;

Route::prefix("/auth")->group(function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::put("/register", [AuthController::class, 'refresh'] );
});