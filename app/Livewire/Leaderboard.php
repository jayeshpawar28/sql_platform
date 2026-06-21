<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Leaderboard extends Component
{
    public $timeframe = 'all_time';

    public function render()
    {
        $query = User::select('users.id', 'users.name', DB::raw('COUNT(DISTINCT submissions.problem_id) as solved_count'))
            ->join('submissions', 'users.id', '=', 'submissions.user_id')
            ->where('submissions.status', 'correct');

        if ($this->timeframe === 'weekly') {
            $query->where('submissions.created_at', '>=', Carbon::now()->startOfWeek());
        } elseif ($this->timeframe === 'monthly') {
            $query->where('submissions.created_at', '>=', Carbon::now()->startOfMonth());
        }

        $leaders = $query->groupBy('users.id', 'users.name')
            ->orderBy('solved_count', 'desc')
            ->take(50)
            ->get();

        return view('livewire.leaderboard', [
            'leaders' => $leaders
        ])->layout('layouts.app');
    }
}
