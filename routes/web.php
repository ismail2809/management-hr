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

Route::get('/politique-confidentialite/pdf', [\App\Http\Controllers\LegalPdfController::class, 'politiqueConfidentialite'])
    ->name('politique-confidentialite.pdf');

Route::get('/mentions-legales', fn () => view('mentions-legales'))
    ->name('mentions-legales');

Route::get('/mentions-legales/pdf', [\App\Http\Controllers\LegalPdfController::class, 'mentionsLegales'])
    ->name('mentions-legales.pdf');

Route::middleware(['auth'])->group(function () {
    Route::get('/documents/{documentRequest}/pdf', [\App\Http\Controllers\DocumentPdfController::class, 'download'])
        ->name('documents.pdf');
    Route::get('/documents/{documentRequest}/preview', [\App\Http\Controllers\DocumentPdfController::class, 'preview'])
        ->name('documents.preview');
    Route::get('/documents/{documentRequest}/download-final', [\App\Http\Controllers\DocumentPdfController::class, 'downloadFinal'])
        ->name('documents.download-final');
});
