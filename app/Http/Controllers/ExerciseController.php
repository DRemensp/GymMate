<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\TrainingPlan;
use Illuminate\Support\Facades\Auth;

class ExerciseController extends Controller
{
    public function index(TrainingPlan $trainingPlan)
    {
        abort_if($trainingPlan->location->user_id !== Auth::id(), 403);

        $exercises = $trainingPlan->exercises()->with('media')->orderBy('name')->get();

        return view('exercises.index', compact('trainingPlan', 'exercises'));
    }

    public function show(Exercise $exercise)
    {
        abort_if($exercise->trainingPlan->location->user_id !== Auth::id(), 403);

        $sessions = $exercise->workoutSessions()->with('sets')->get();

        return view('exercises.show', compact('exercise', 'sessions'));
    }

    public function destroy(Exercise $exercise)
    {
        abort_if($exercise->trainingPlan->location->user_id !== Auth::id(), 403);

        $exercise->delete();

        return redirect()->route('training-plans.exercises.index', $exercise->training_plan_id);
    }
}
