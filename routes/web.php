<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (app()->environment('production')) {
        return redirect('/admin');
    }
    return view('welcome');
});

Route::get('/politique-confidentialite', fn () => view('politique-confidentialite'))
    ->name('politique-confidentialite');

Route::get('/mentions-legales', fn () => view('mentions-legales'))
    ->name('mentions-legales');

Route::middleware(['auth'])->group(function () {
    Route::get('/documents/{documentRequest}/pdf', [\App\Http\Controllers\DocumentPdfController::class, 'download'])
        ->name('documents.pdf');
    Route::get('/documents/{documentRequest}/preview', [\App\Http\Controllers\DocumentPdfController::class, 'preview'])
        ->name('documents.preview');
});
