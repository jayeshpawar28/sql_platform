<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-3 mb-2 mb-md-0">
            <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search problems...">
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <select wire:model.live="difficulty" class="form-select">
                <option value="">All Difficulties</option>
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
            </select>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <select wire:model.live="topic" class="form-select">
                <option value="">All Topics</option>
                @foreach($topics as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <select wire:model.live="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="todo">To Do</option>
                <option value="attempted">Attempted</option>
                <option value="solved">Solved</option>
            </select>
        </div>
        <div class="col-md-2 text-md-end">
            <button wire:click="resetFilters" class="btn btn-outline-primary w-100">
                Reset Filters
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Status</th>
                        <th>Title</th>
                        <th>Difficulty</th>
                        <th>Topic</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($problems as $problem)
                        @php
                            $solved = $problem->submissions->contains('status', 'correct');
                            $attempted = $problem->submissions->contains('status', 'incorrect') && !$solved;
                        @endphp
                        <tr>
                            <td>
                                @if($solved)
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                @elseif($attempted)
                                    <i class="bi bi-arrow-repeat text-warning fs-5"></i>
                                @else
                                    <i class="bi bi-circle text-muted fs-5"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('problems.show', $problem) }}" class="text-decoration-none fw-bold text-body">
                                    {{ $problem->title }}
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $problem->difficulty === 'easy' ? 'bg-success' : ($problem->difficulty === 'medium' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ ucfirst($problem->difficulty) }}
                                </span>
                            </td>
                            <td>{{ $problem->topic }}</td>
                            <td>
                                <a href="{{ route('problems.show', $problem) }}" class="btn btn-sm btn-outline-primary">
                                    Solve
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No problems found matching your criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $problems->links() }}
    </div>
</div>
