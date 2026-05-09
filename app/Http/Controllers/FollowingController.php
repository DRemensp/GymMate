<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FollowingController extends Controller
{
    public function index(): View
    {
        $following = Auth::user()->following()->get();

        return view('following', [
            'following' => $following,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->input('user_id'));

        if ($user->id !== Auth::id()) {
            Auth::user()->following()->syncWithoutDetaching([$user->id]);
        }

        return back();
    }

    public function destroy(int $userId): RedirectResponse
    {
        Auth::user()->following()->detach($userId);

        return back();
    }
}
