<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SearchUsers extends Component
{
    public string $query = '';

    public function follow(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id !== Auth::id()) {
            Auth::user()->following()->syncWithoutDetaching([$user->id]);
        }
    }

    public function unfollow(int $userId): void
    {
        Auth::user()->following()->detach($userId);
    }

    public function render()
    {
        $results = strlen($this->query) >= 1
            ? User::where('name', 'like', '%' . $this->query . '%')
                ->where('id', '!=', Auth::id())
                ->limit(20)
                ->get()
            : collect();

        $followingIds = Auth::user()->following()->pluck('users.id');

        return view('livewire.search-users', [
            'results'      => $results,
            'followingIds' => $followingIds,
        ]);
    }
}
