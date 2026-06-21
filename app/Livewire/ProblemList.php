<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Problem;
use Illuminate\Support\Facades\Auth;

class ProblemList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $difficulty = '';
    public $topic = '';
    public $status = '';

    public function updatingDifficulty() { $this->resetPage(); }
    public function updatingTopic() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->reset(['search', 'difficulty', 'topic', 'status']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();
        $topics = Problem::select('topic')->distinct()->whereNotNull('topic')->pluck('topic');

        $problems = Problem::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->difficulty, function ($query) {
                $query->where('difficulty', $this->difficulty);
            })
            ->when($this->topic, function ($query) {
                $query->where('topic', $this->topic);
            })
            ->when($this->status, function ($query) use ($userId) {
                if ($this->status === 'solved') {
                    $query->whereHas('submissions', function ($q) use ($userId) {
                        $q->where('user_id', $userId)->where('status', 'correct');
                    });
                } elseif ($this->status === 'attempted') {
                    $query->whereHas('submissions', function ($q) use ($userId) {
                        $q->where('user_id', $userId)->where('status', 'incorrect');
                    })->whereDoesntHave('submissions', function ($q) use ($userId) {
                        $q->where('user_id', $userId)->where('status', 'correct');
                    });
                } elseif ($this->status === 'todo') {
                    $query->whereDoesntHave('submissions', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
                }
            })
            ->with(['submissions' => function($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->paginate(10);

        return view('livewire.problem-list', [
            'problems' => $problems,
            'topics' => $topics
        ])->layout('layouts.app');
    }
}
