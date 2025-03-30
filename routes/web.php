<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('ReservationForm');
})->name('home');

Route::post('/reservation-summary', function () {
    return Inertia::render('ReservationSummary', [
        'formData' => request()->all()
    ]);
})->name('reservation.summary');

Route::get('/reservation-summary', function () {
    if (session()->hasOldInput()) {
        return Inertia::render('ReservationSummary', [
            'formData' => session()->getOldInput()
        ]);
    }

    return redirect()->route('home');
})->name('reservation.summary.get');


Route::get('/reservation-confirmation/{bookingNumber}', function ($bookingNumber) {
    return Inertia::render('ReservationConfirmation', [
        'bookingNumber' => $bookingNumber
    ]);
})->name('reservation.confirmation');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
