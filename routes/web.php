<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('posts', PostController::class)->only(['index', 'show']);

Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class)->except(['index', 'show']);
});

// Route::get('/posts', function () {
//     return view('posts.index');
// })->name('posts.index');

// Route::get('/posts/create', function () {
//     return view('posts.create');
// })->name('posts.create');

// Route::get('/posts/show', function () {
//     return view('posts.show');
// });

// Route::get('/posts/edit', function () {
//     return view('posts.edit');
// });

Route::post('/session/clear', function () {
    session()->forget(['status', 'title', 'message']);
    return response()->json(['cleared' => true]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
