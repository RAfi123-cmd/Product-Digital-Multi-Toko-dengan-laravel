<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/{username}', [FrontendController::class, 'index'])->name('index');
