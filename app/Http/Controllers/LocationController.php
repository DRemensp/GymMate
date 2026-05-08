<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Auth::user()->locations()->with('media')->orderBy('name')->get();

        return view('dashboard', compact('locations'));
    }

    public function destroy(Location $location)
    {
        abort_if($location->user_id !== Auth::id(), 403);

        $location->delete();

        return redirect()->route('dashboard');
    }
}
