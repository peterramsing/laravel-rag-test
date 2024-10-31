<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RagTestController;

Route::get('/', [RagTestController::class, 'index']);
Route::post('/', [RagTestController::class, 'store'])->name('ragtest.store');
