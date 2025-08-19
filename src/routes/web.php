<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Ogwebsolutions\FormBuilder\Http\Controllers\FormController;
use Ogwebsolutions\FormBuilder\Http\Controllers\SubmissionController;

Route::middleware(config('ogformbuilder.admin_middleware'))->prefix(config('ogformbuilder.admin_route_prefix'))->group(function () {
    Route::get('/', [FormController::class, 'index'])->name('formbuilder.index');
    Route::get('/create', [FormController::class, 'create'])->name('formbuilder.create');
    Route::post('/store', [FormController::class, 'store'])->name('formbuilder.store');
    Route::get('/{id}/edit', [FormController::class, 'edit'])->name('formbuilder.edit');
    Route::put('/{id}/update', [FormController::class, 'update'])->name('formbuilder.update');
    Route::get('/{id}/settings', [FormController::class, 'settings'])->name('formbuilder.settings');
    Route::post('/{id}/settings', [FormController::class, 'saveSettings'])->name('formbuilder.settings.save');
    Route::get('/{id}/submissions', [SubmissionController::class, 'index'])->name('formbuilder.submissions');
    Route::delete('/{id}', [FormController::class, 'destroy'])->name('formbuilder.destroy');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('formbuilder.submissions.show');
    Route::delete('/submissions/{submission}', [SubmissionController::class, 'destroy'])->name('formbuilder.submissions.destroy');
    Route::get('/forms/{form}/submissions/export', [SubmissionController::class, 'exportCsv'])->name('formbuilder.submissions.export');
    // ✅ Serve package images securely (without publishing)
    Route::get('/assets/images/{path?}', function ($path) {
        $safePath = str_replace(['..', './'], '', $path); // basic protection
        $fullPath = __DIR__ . '/../public/images/' . $safePath;

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return Response::file($fullPath, ['Content-Type' => mime_content_type($fullPath)]);
    })->where('path', '.*')->name('formbuilder.assets.image');

});

Route::middleware(config('ogformbuilder.form_middleware'))->prefix(config('ogformbuilder.route_prefix'))->group(function () {
    Route::post('/form/{form}', [SubmissionController::class, 'store'])->name('formbuilder.form.store');
});
