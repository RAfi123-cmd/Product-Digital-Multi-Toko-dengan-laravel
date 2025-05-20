<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/{username}', [FrontendController::class, 'index'])->name('index');
Route::get('/{username}/product/{id}', [ProductController::class, 'show'])->name('product.show');
