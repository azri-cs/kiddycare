<?php

use App\Http\Controllers\API\BabysittingRequestController;
use App\Http\Controllers\API\CareRecipientController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('api')->group(function () {
    // Public routes
    Route::post('babysitting-requests', [BabysittingRequestController::class, 'store']);
});
