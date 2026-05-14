<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TourController extends Controller
{
    private const STEPS = ['locations', 'plans', 'exercises', 'logging', 'weekly', 'analyse', 'profile', 'cardio', 'following', 'settings', 'data_export'];

    public function complete(string $step): JsonResponse
    {
        if (!in_array($step, self::STEPS, true)) {
            abort(422);
        }

        Auth::user()->getOrCreateTour()->update([$step => true]);

        return response()->json(['ok' => true]);
    }

    public function skip(): JsonResponse
    {
        Auth::user()->getOrCreateTour()->update(array_fill_keys(self::STEPS, true));

        return response()->json(['ok' => true]);
    }

    public function reset(): RedirectResponse
    {
        Auth::user()->getOrCreateTour()->update(array_fill_keys(self::STEPS, false));

        return back();
    }
}
