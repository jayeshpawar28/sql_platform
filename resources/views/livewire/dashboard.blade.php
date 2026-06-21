<div class="container py-4 py-lg-5">

    <style>
        .page-head h2 { font-size: 1.7rem; margin-bottom: .25rem; }
        .page-head p { color: var(--text-muted); font-size: .92rem; }

        .stat-card {
            background: var(--surface-solid);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 110px; height: 110px;
            border-radius: 50%;
            opacity: .12;
            filter: blur(8px);
        }
        .stat-card.c-violet::after { background: var(--violet); }
        .stat-card.c-cyan::after { background: var(--cyan); }
        .stat-card.c-success::after { background: var(--success); }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: var(--radius-sm);
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: .9rem;
            font-size: 1.2rem;
        }
        .stat-card.c-violet .stat-icon { background: var(--violet-soft); color: var(--violet); }
        .stat-card.c-cyan .stat-icon { background: var(--cyan-soft); color: var(--cyan); }
        .stat-card.c-success .stat-icon { background: var(--success-soft); color: var(--success); }

        .stat-value { font-family: var(--font-display); font-size: 2.2rem; font-weight: 700; line-height: 1; margin-bottom: .35rem; }
        .stat-label { color: var(--text-muted); font-size: .85rem; }

        .panel { background: var(--surface-solid); border: 1px solid var(--border); border-radius: var(--radius); }
        .panel-header { padding: 1.1rem 1.4rem; border-bottom: 1px solid var(--border); display:flex; justify-content: space-between; align-items: center; }
        .panel-header h5 { font-size: 1.02rem; margin: 0; }
        .panel-body { padding: 1.4rem; }

        .topic-row { margin-bottom: 1.3rem; }
        .topic-row:last-child { margin-bottom: 0; }
        .topic-row-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: .45rem; }
        .topic-name { font-weight: 600; font-size: .92rem; }
        .topic-frac { font-family: var(--font-mono); font-size: .78rem; color: var(--text-faint); }

        .progress-track { height: 8px; background: var(--surface-2); border-radius: 999px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--violet), var(--cyan)); transition: width .6s ease; }

        .sub-row { display: flex; align-items: center; gap: .9rem; padding: .85rem 1.4rem; border-bottom: 1px solid var(--border); }
        .sub-row:last-child { border-bottom: none; }
        .sub-title { font-weight: 600; font-size: .9rem; }
        .sub-time { font-size: .76rem; color: var(--text-faint); font-family: var(--font-mono); }

        .status-badge { font-size: .7rem; padding: .35em .65em; white-space: nowrap; }
        .status-accepted { background: var(--success-soft); color: var(--success); }
        .status-wrong { background: var(--warning-soft); color: var(--warning); }
        .status-error { background: var(--danger-soft); color: var(--danger); }

        .empty-mini { padding: 2.5rem 1rem; text-align: center; color: var(--text-muted); }
        .empty-mini i { font-size: 1.8rem; color: var(--text-faint); display: block; margin-bottom: .6rem; }
    </style>

    <div class="page-head mb-4">
        <h2 class="fw-bold">My dashboard</h2>
        <p class="mb-0">Your progress at a glance.</p>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="stat-card c-violet">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-value">{{ $totalSolved }}</div>
                <div class="stat-label">Problems solved</div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="stat-card c-cyan">
                <div class="stat-icon"><i class="bi bi-code-slash"></i></div>
                <div class="stat-value">{{ $totalSubmissions }}</div>
                <div class="stat-label">Total submissions</div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="stat-card c-success">
                <div class="stat-icon"><i class="bi bi-bullseye"></i></div>
                <div class="stat-value">{{ $accuracy }}%</div>
                <div class="stat-label">Accuracy rate</div>
            </div>
        </div>
    </div>

    <!-- Topic progress -->
    <div class="panel mb-4">
        <div class="panel-header">
            <h5 class="fw-bold">Topic progress</h5>
        </div>
        <div class="panel-body">
            @forelse($topics as $topic)
                @php
                    $percentage = $topic->total_problems > 0 ? ($topic->problems_solved / $topic->total_problems) * 100 : 0;
                @endphp
                <div class="topic-row">
                    <div class="topic-row-head">
                        <span class="topic-name">{{ $topic->topic }}</span>
                        <span class="topic-frac">{{ $topic->problems_solved }} / {{ $topic->total_problems }}</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @empty
                <div class="empty-mini">
                    <i class="bi bi-bar-chart"></i>
                    No progress tracked yet — start solving problems!
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent submissions -->
    <div class="panel">
        <div class="panel-header">
            <h5 class="fw-bold">Recent submissions</h5>
            <a href="{{ route('problems.index') }}" class="btn btn-outline-primary btn-sm">Solve more</a>
        </div>
        <div>
            @forelse($recentSubmissions as $submission)
                <div class="sub-row">
                    <div class="flex-grow-1">
                        <a href="{{ route('problems.show', $submission->problem) }}" class="text-decoration-none">
                            <div class="sub-title text-body">{{ $submission->problem->title }}</div>
                        </a>
                        <div class="sub-time">{{ $submission->created_at->diffForHumans() }}</div>
                    </div>
                    @if($submission->status === 'correct')
                        <span class="badge status-badge status-accepted">Accepted</span>
                    @elseif($submission->status === 'incorrect')
                        <span class="badge status-badge status-wrong">Wrong answer</span>
                    @else
                        <span class="badge status-badge status-error">Error</span>
                    @endif
                </div>
            @empty
                <div class="empty-mini">
                    <i class="bi bi-clock-history"></i>
                    No recent submissions found.
                </div>
            @endforelse
        </div>
    </div>
</div>