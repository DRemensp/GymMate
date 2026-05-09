<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (Auth::user()->hasCompleteProfile()) {
            return redirect()->route('dashboard');
        }

        return view('onboarding');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'weight_kg' => ['required', 'numeric', 'min:30', 'max:300'],
            'height_cm' => ['required', 'integer', 'min:100', 'max:250'],
            'gender'    => ['required', 'in:männlich,weiblich,divers'],
        ]);

        $request->user()->update($request->only('weight_kg', 'height_cm', 'gender'));

        return redirect()->route('dashboard');
    }
}
