<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvController;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::post('/upload-csv', [CsvController::class, 'upload'])->name('csv.upload');
Route::get('/', [CsvController::class, 'index'])->name('csv.index');
Route::get('/status', [CsvController::class, 'status'])->name('csv.status');
