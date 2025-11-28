<?php

use Illuminate\Support\Facades\Route;
use Skolaris\AcademicReports\Http\Controllers\ReportController;

Route::group(['middleware' => ['web'], 'prefix' => 'reports'], function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/student/{studentId}/term/{termId}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/download/{reportId}', [ReportController::class, 'download'])->name('reports.download');
    Route::get('/class/{className}/term/{termId}', [ReportController::class, 'classReport'])->name('reports.class');
});
