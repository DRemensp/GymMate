<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\TrainingPlan;
use Illuminate\Support\Facades\Auth;

class TrainingPlanController extends Controller
{
    public function index(Location $location)
    {
        abort_if($location->user_id !== Auth::id(), 403);

        $plans = $location->trainingPlans()->with('media')->latest()->get();

        return view('training-plans.index', compact('location', 'plans'));
    }

    public function destroy(TrainingPlan $trainingPlan)
    {
        abort_if($trainingPlan->location->user_id !== Auth::id(), 403);

        $trainingPlan->delete();

        return redirect()->route('locations.training-plans.index', $trainingPlan->location_id);
    }
}
