<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Submission;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $userId = Auth::id();

        // Stats
        $totalSolved = Submission::where('user_id', $userId)
            ->where('status', 'correct')
            ->distinct('problem_id')
            ->count();
            
        $totalSubmissions = Submission::where('user_id', $userId)->count();
        
        $accuracy = $totalSubmissions > 0 
            ? round((Submission::where('user_id', $userId)->where('status', 'correct')->count() / $totalSubmissions) * 100) 
            : 0;

        // Progress by Topic
        $topics = UserProgress::where('user_id', $userId)->get();

        // Recent Submissions
        $recentSubmissions = Submission::with('problem')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'totalSolved' => $totalSolved,
            'totalSubmissions' => $totalSubmissions,
            'accuracy' => $accuracy,
            'topics' => $topics,
            'recentSubmissions' => $recentSubmissions,
        ])->layout('layouts.app');
    }

    public function toggleNotification()
    {
       $userId = Auth::id();
    }
}
