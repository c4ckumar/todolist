<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TodolistController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::resource('/', TodolistController::class);
Route::post('/getLists', [TodolistController::class, 'getLists']);
Route::post('/remove', [TodolistController::class, 'remove']);
Route::post('/change', [TodolistController::class, 'change']);
