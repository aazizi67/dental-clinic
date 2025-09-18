<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-appointments', function () {
    return 'Appointments route is working!';
});

Route::get('/test-appointments/booking', function () {
    return 'Booking route is working!';
});