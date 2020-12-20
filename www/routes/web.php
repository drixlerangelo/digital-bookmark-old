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
    Route::get('/', [PageController::class, 'showAuth'])->name('user.auth');

    Route::prefix('user')->group(function () {
        Route::post('login', [UserController::class, 'loginUser'])->name('user.login');
        Route::post('signup', [UserController::class, 'registerUser'])->name('user.signup');
    });
});
