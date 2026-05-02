<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/payrolls/{payroll}/pdf', [\App\Http\Controllers\PayrollPdfController::class, 'download'])
        ->name('payrolls.pdf');
    Route::get('/payrolls/bulk-pdf', [\App\Http\Controllers\PayrollBulkPdfController::class, 'download'])
        ->name('payrolls.bulk-pdf');

    Route::get('/documents/{documentRequest}/pdf', [\App\Http\Controllers\DocumentPdfController::class, 'download'])
        ->name('documents.pdf');
});
