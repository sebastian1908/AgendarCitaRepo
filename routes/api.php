<?php

use Illuminate\Support\Facades\Route;
use Src\Adapter\Http\UserController;
use Src\Adapter\Http\CitaController;
use Src\Adapter\Http\Middleware\JWTAuthMiddleware;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/users', [UserController::class, 'createUser'])->middleware(JWTAuthMiddleware::class);

Route::middleware([JWTAuthMiddleware::class])->group(function () {
    Route::post('/citas/agendar', [CitaController::class, 'agendar']);
    Route::get('/citas/listar', [CitaController::class, 'listar']);
});
