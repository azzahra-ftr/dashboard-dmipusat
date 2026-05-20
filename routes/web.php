<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\WpPostController;
use App\Http\Controllers\Admin\DashboardController; 
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;  
use App\Http\Controllers\Admin\EventController;


// --- DIRECT BASE URL KE LOGIN --- ///
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/forgot-password', function() {
    return redirect()->route('login');
})->name('password.request');

// ---  AUTENTIKASI --- //
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE ADMIN DASHBOARD  --- //
Route::prefix('admin')->middleware('auth')->group(function () {


// --- Dashboard utama --- // 
Route::get('/dashboard', [DashboardController::class, 'index'])->name('home.home');


// --- MANAJEMEN ALL POST (BERITA) --- //
Route::controller(WpPostController::class)->group(function () {
Route::get('/wp-posts', 'index')->name('posts.index');          
Route::get('/wp-posts/create', 'create')->name('posts.create');
Route::post('/wp-posts/store', 'store')->name('posts.store');
Route::get('/wp-posts/edit/{id}', 'edit')->name('posts.edit');
Route::post('/wp-posts/update/{id}', 'update')->name('posts.update');
Route::delete('/wp-posts/delete/{id}', 'destroy')->name('posts.delete');

});

    // --- MANAJEMEN EVENT --- //
Route::controller(EventController::class)->group(function (){
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
    // Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');
});

});
