<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrainingPlanController;
use App\Http\Controllers\WeeklyScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [LocationController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/analyse', [AnalyticsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('analytics');

Route::middleware('auth')->group(function () {
    Route::get('/trainingsplan', [WeeklyScheduleController::class, 'index'])->name('weekly-schedule');
    Route::post('/trainingsplan', [WeeklyScheduleController::class, 'update'])->name('weekly-schedule.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/locations/{location}/training-plans', [TrainingPlanController::class, 'index'])
        ->name('locations.training-plans.index');

    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])
        ->name('locations.destroy');

    Route::delete('/training-plans/{trainingPlan}', [TrainingPlanController::class, 'destroy'])
        ->name('training-plans.destroy');

    Route::get('/training-plans/{trainingPlan}/exercises', [ExerciseController::class, 'index'])
        ->name('training-plans.exercises.index');

    Route::get('/exercises/{exercise}', [ExerciseController::class, 'show'])
        ->name('exercises.show');

    Route::delete('/exercises/{exercise}', [ExerciseController::class, 'destroy'])
        ->name('exercises.destroy');

});

require __DIR__.'/auth.php';
