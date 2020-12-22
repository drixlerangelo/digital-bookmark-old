<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::middleware('guest')->group(function () {
    Route::get('/', [PageController::class, 'showAuth'])->name('auth');

    Route::prefix('user')->group(function () {
        Route::post('login', [UserController::class, 'loginUser'])->name('login');
        Route::post('signup', [UserController::class, 'registerUser'])->name('signup');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [PageController::class, 'showHome'])->name('home');

    Route::prefix('user')->group(function () {
        Route::get('logout', [UserController::class, 'logoutUser'])->name('logout');
    });
});
