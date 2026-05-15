<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CardioController;
use App\Http\Controllers\DataPortabilityController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\PrefetchController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\TrainingPlanController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\WeeklyScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/offline', fn() => view('offline'))->name('offline');
Route::get('/ping', fn() => response('', 204));

Route::get('/u/{name}', [PublicProfileController::class, 'show'])->name('profile.public');

Route::get('/standorte', [LocationController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/analyse', [AnalyticsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('analytics');

Route::middleware('auth')->group(function () {
    Route::get('/onboarding',  [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    Route::get('/trainingsplan', [WeeklyScheduleController::class, 'index'])->name('weekly-schedule');
    Route::post('/trainingsplan', [WeeklyScheduleController::class, 'update'])->name('weekly-schedule.update');

    Route::get('/einstellungen', [ProfileController::class, 'edit'])->name('settings.edit');
    Route::patch('/einstellungen', [ProfileController::class, 'update'])->name('settings.update');
    Route::post('/einstellungen/avatar', [ProfileController::class, 'updateAvatar'])->name('settings.avatar');
    Route::delete('/einstellungen', [ProfileController::class, 'destroy'])->name('settings.destroy');

    Route::get('/following', [FollowingController::class, 'index'])->name('following');
    Route::post('/following', [FollowingController::class, 'store'])->name('following.store');
    Route::delete('/following/{userId}', [FollowingController::class, 'destroy'])->name('following.destroy');

    Route::get('/standorte/{location}/trainingskategorie', [TrainingPlanController::class, 'index'])
        ->name('locations.training-plans.index');

    Route::delete('/standorte/{location}', [LocationController::class, 'destroy'])
        ->name('locations.destroy');

    Route::delete('/trainingskategorie/{trainingPlan}', [TrainingPlanController::class, 'destroy'])
        ->name('training-plans.destroy');

    Route::get('/trainingskategorie/{trainingPlan}/uebungen', [ExerciseController::class, 'index'])
        ->name('training-plans.exercises.index');

    Route::get('/uebungen/{exercise}', [ExerciseController::class, 'show'])
        ->name('exercises.show');

    Route::delete('/uebungen/{exercise}', [ExerciseController::class, 'destroy'])
        ->name('exercises.destroy');

    Route::get('/cardio', [CardioController::class, 'index'])->name('cardio');
    Route::delete('/cardio/{cardioSession}', [CardioController::class, 'destroy'])->name('cardio.destroy');

    Route::post('/tour/skip',     [TourController::class, 'skip'])->name('tour.skip');
    Route::post('/tour/reset',    [TourController::class, 'reset'])->name('tour.reset');
    Route::post('/tour/{step}',   [TourController::class, 'complete'])->name('tour.complete');

    Route::get('/data',                 [DataPortabilityController::class, 'index'])->name('data');
    Route::get('/data/export/workouts', [DataPortabilityController::class, 'exportWorkouts'])->name('data.export.workouts');
    Route::get('/data/export/cardio',   [DataPortabilityController::class, 'exportCardio'])->name('data.export.cardio');
    Route::post('/data/import/workouts',[DataPortabilityController::class, 'importWorkouts'])->name('data.import.workouts');
    Route::post('/data/import/cardio',  [DataPortabilityController::class, 'importCardio'])->name('data.import.cardio');

    Route::post('/sync', [SyncController::class, 'handle'])->name('sync');

    Route::get('/prefetch-urls', [PrefetchController::class, 'urls'])->name('prefetch.urls');

});

require __DIR__.'/auth.php';
