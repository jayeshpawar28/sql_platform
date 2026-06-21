<div class="container py-4 py-lg-5 px-3 px-sm-4">

    <style>
        .page-head h2 { font-size: 1.7rem; margin-bottom: .25rem; }
        .page-head p { color: var(--text-muted); font-size: .92rem; }

        .filter-bar {
            background: var(--surface-solid);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1rem;
        }

        .problem-card {
            background: var(--surface-solid);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .problem-row {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            color: inherit;
            transition: background-color .15s ease;
        }
        .problem-row:last-child { border-bottom: none; }
        .problem-row:hover { background: var(--violet-soft); }

        .status-icon { font-size: 1.25rem; flex-shrink: 0; width: 22px; text-align: center; }
        .status-solved { color: var(--success); }
        .status-attempted { color: var(--warning); }
        .status-todo { color: var(--text-faint); }

        .problem-title-wrap { min-width: 0; }
        .problem-title {
            font-weight: 600;
            color: var(--text);
            font-size: .98rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .problem-meta { font-size: .8rem; color: var(--text-faint); font-family: var(--font-mono); }

        .diff-badge { font-size: .7rem; padding: .35em .65em; flex-shrink: 0; }
        .diff-easy { background: var(--success-soft); color: var(--success); }
        .diff-medium { background: var(--warning-soft); color: var(--warning); }
        .diff-hard { background: var(--danger-soft); color: var(--danger); }

        .solve-btn {
            font-size: .82rem;
            padding: .4rem .9rem;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .empty-state { padding: 3.5rem 1rem; text-align: center; }
        .empty-state i { font-size: 2.4rem; color: var(--text-faint); }

        /* Mobile: two clean rows instead of one cramped wrapped flex line */
        @media (max-width: 575.98px) {
            .problem-row {
                flex-wrap: wrap;
                row-gap: .75rem;
                padding: .9rem 1rem;
            }
            .problem-title-wrap {
                flex: 1 1 calc(100% - 22px - .9rem);
                order: 1;
            }
            .problem-title { white-space: normal; }
            .problem-badges {
                order: 2;
                flex: 0 0 auto;
                margin-left: calc(22px + .9rem);
            }
            .solve-btn {
                order: 3;
                margin-left: auto;
            }
        }
    </style>

    <div class="page-head mb-4">
        <h2 class="fw-bold">Problems</h2>
        <p class="mb-0">Filter by topic and difficulty, then jump straight into the sandbox.</p>
    </div>

    <div class="filter-bar mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-3">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color: var(--text-faint); font-size:.85rem;"></i>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control ps-4" style="padding-left:2.1rem;" placeholder="Search problems...">
                </div>
            </div>
            <div class="col-12 col-md-2">
                <select wire:model.live="difficulty" class="form-select">
                    <option value="">All difficulty</option>
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <select wire:model.live="topic" class="form-select">
                    <option value="">All topics</option>
                    @foreach($topics as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-7 col-md-2">
                <select wire:model.live="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="todo">To do</option>
                    <option value="attempted">Attempted</option>
                    <option value="solved">Solved</option>
                </select>
            </div>
            <div class="col-5 col-md-2">
                <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="problem-card">
        @forelse($problems as $problem)
            @php
                $solved = $problem->submissions->contains('status', 'correct');
                $attempted = $problem->submissions->contains('status', 'incorrect') && !$solved;
                $diffClass = $problem->difficulty === 'easy' ? 'diff-easy' : ($problem->difficulty === 'medium' ? 'diff-medium' : 'diff-hard');
            @endphp
            <a href="{{ route('problems.show', $problem) }}" class="problem-row">
                <span class="status-icon {{ $solved ? 'status-solved' : ($attempted ? 'status-attempted' : 'status-todo') }}">
                    <i class="bi {{ $solved ? 'bi-check-circle-fill' : ($attempted ? 'bi-arrow-repeat' : 'bi-circle') }}"></i>
                </span>

                <div class="problem-title-wrap flex-grow-1">
                    <div class="problem-title">{{ $problem->title }}</div>
                    <div class="problem-meta">{{ $problem->topic }}</div>
                </div>

                <div class="problem-badges">
                    <span class="badge diff-badge {{ $diffClass }}">{{ ucfirst($problem->difficulty) }}</span>
                </div>

                <span class="btn btn-outline-primary solve-btn">Solve <i class="bi bi-arrow-right ms-1"></i></span>
            </a>
        @empty
            <div class="empty-state">
                <i class="bi bi-inbox d-block mb-3"></i>
                <p class="text-muted mb-0">No problems match your filters. Try resetting them.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $problems->links() }}
    </div>
</div>