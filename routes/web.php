<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('login')->middleware(['guest']);

Route::get('/register', function () {
    return view('register');
})->name('register')->middleware(['guest']);

Route::get('/chat', \App\Http\Controllers\ChatIndexController::class)->name('chat')->middleware(['auth']);

Route::post('/user/register', \App\Http\Controllers\UserRegisterController::class)->name('user.register');
Route::post('/user/login', \App\Http\Controllers\UserLoginController::class)->name('user.login');

Route::post('/connection', \App\Http\Controllers\ConnectionStoreController::class)->name('connection.add');
