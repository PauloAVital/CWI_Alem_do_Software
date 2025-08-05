<?php

use App\Http\Controllers\Api\Usuarios;
use Illuminate\Support\Facades\Route;


Route::get('/usuarios', [Usuarios::class, 'index']);
Route::get('/usuarios/{id}', [Usuarios::class, 'show']);
Route::post('/usuarios', [Usuarios::class, 'store']);
Route::put('/usuarios/{id}', [Usuarios::class, 'update']);
Route::delete('/usuarios/{id}', [Usuarios::class, 'destroy']);

//Route::get('/usuarios', [App\Http\Controllers\Api\Usuarios::class, 'index']); // Correta