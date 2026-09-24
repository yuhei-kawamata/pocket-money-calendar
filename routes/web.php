<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChoreController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/calendars', [CalendarController::class, 'index'])->name('calendars.index');
    Route::get('/calendars/dashboard', [CalendarController::class, 'showDashboard'])->name('calendars.dashboard');

    Route::get('/chores', [ChoreController::class, 'index'])->name('chores.index');
    Route::post('/chores', [ChoreController::class, 'store'])->name('chores.store');
    Route::patch('/chores/{id}', [ChoreController::class, 'update'])->name('chores.update');
    Route::delete('/chores/{id}', [ChoreController::class, 'destroy'])->name('chores.destroy');
});
