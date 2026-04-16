<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciseLogController extends Controller
{
    public function store(Request $request, Exercise $exercise)
    {
        abort_if($exercise->trainingPlan->location->user_id !== Auth::id(), 403);

        $request->validate([
            'weight' => ['required', 'numeric', 'min:0', 'max:9999'],
            'reps'   => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        $exercise->logs()->create([
            'weight'    => $request->weight,
            'reps'      => $request->reps,
            'logged_at' => now(),
        ]);

        return back();
    }
}
