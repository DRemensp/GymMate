<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class PrefetchController extends Controller
{
    public function urls()
    {
        $userId = Auth::id();

        $locations = Location::where('user_id', $userId)
            ->with('trainingPlans.exercises')
            ->get();

        $urls = [
            route('dashboard'),
            route('analytics'),
            route('weekly-schedule'),
            route('cardio'),
            route('data'),
            route('following'),
        ];

        foreach ($locations as $location) {
            $urls[] = route('locations.training-plans.index', $location);
            foreach ($location->trainingPlans as $plan) {
                $urls[] = route('training-plans.exercises.index', $plan);
                foreach ($plan->exercises as $exercise) {
                    $urls[] = route('exercises.show', $exercise);
                }
            }
        }

        return response()->json($urls);
    }
}
