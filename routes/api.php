<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\v1\RuleEngineController;
use Illuminate\Support\Facades\Route;

Route::post('login', [ AuthController::class, 'login' ]);

Route::prefix('v1')->group(function() {
    Route::post('file-upload', [ RuleEngineController::class, 'fileUpload' ]);
    Route::post('file-scan', [ RuleEngineController::class, 'scanFile' ]);
    Route::get('upload-status', [ RuleEngineController::class, 'uploadStatus' ]);
});
