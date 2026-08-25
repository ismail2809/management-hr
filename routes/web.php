<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/documents/{documentRequest}/pdf', [\App\Http\Controllers\DocumentPdfController::class, 'download'])
        ->name('documents.pdf');
    Route::get('/documents/{documentRequest}/preview', [\App\Http\Controllers\DocumentPdfController::class, 'preview'])
        ->name('documents.preview');
});
