<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\WpPostController;
use App\Http\Controllers\Admin\DashboardController; 
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;  


// --- HALAMAN UMUM ---
Route::get('/', function () {
    return redirect()->route('home.home');
});

// --- RUTE AUTENTIKASI ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE ADMIN DASHBOARD  --- //
Route::prefix('admin')->group(function () {

    // Dashboard utama 
Route::get('/dashboard', [DashboardController::class, 'index'])->name('home.home');


    // Manajemen Berita 
Route::controller(WpPostController::class)->group(function () {
Route::get('/wp-posts', 'index')->name('posts.index');          
Route::get('/wp-posts/create', 'create')->name('posts.create');
Route::post('/wp-posts/store', 'store')->name('posts.store');
Route::get('/wp-posts/edit/{id}', 'edit')->name('posts.edit');
Route::post('/wp-posts/update/{id}', 'update')->name('posts.update');
Route::delete('/wp-posts/delete/{id}', 'destroy')->name('posts.delete');

Route::get('/events', 'eventIndex')->name('events.event');

    
    });
});