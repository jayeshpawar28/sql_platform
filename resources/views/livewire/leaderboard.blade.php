<div class="container py-4 py-lg-5">

    <style>
        .lb-head h2 { font-size: 1.7rem; margin-bottom: .25rem; }
        .lb-head p { color: var(--text-muted); font-size: .92rem; }

        .timeframe-group .btn-check:checked + .btn {
            background: var(--violet);
            border-color: var(--violet);
            color: #fff;
        }
        .timeframe-group .btn {
            border-color: var(--border-strong);
            color: var(--text-muted);
            font-weight: 500;
        }

        .lb-panel { background: var(--surface-solid); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }

        .lb-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.3rem;
            border-bottom: 1px solid var(--border);
            transition: background-color .15s ease;
        }
        .lb-row:last-child { border-bottom: none; }
        .lb-row:hover { background: var(--violet-soft); }
        .lb-row.top3 { background: linear-gradient(90deg, var(--violet-soft), transparent 60%); }

        .lb-rank {
            width: 38px;
            flex-shrink: 0;
            text-align: center;
            font-family: var(--font-mono);
            font-weight: 700;
            color: var(--text-faint);
            font-size: 1rem;
        }
        .lb-rank .bi { font-size: 1.5rem; }
        .rank-gold { color: #facc15; }
        .rank-silver { color: #c4c9d4; }
        .rank-bronze { color: #cd7f32; }

        .lb-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            background: var(--surface-2);
            border: 1px solid var(--border);
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: .85rem;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        .lb-name { font-weight: 600; font-size: .98rem; display: flex; align-items: center; gap: .5rem; }
        .you-badge { font-size: .65rem; padding: .25em .55em; background: var(--violet); color: #fff; }

        .lb-score { text-align: right; }
        .lb-score-num { font-family: var(--font-display); font-size: 1.3rem; font-weight: 700; color: var(--success); line-height: 1; }
        .lb-score-label { font-size: .72rem; color: var(--text-faint); margin-top: .15rem; }

        .empty-mini { padding: 3.5rem 1rem; text-align: center; color: var(--text-muted); }
        .empty-mini i { font-size: 2rem; color: var(--text-faint); display: block; margin-bottom: .8rem; opacity: .6; }

        @media (max-width: 575.98px) {
            .lb-row { padding: .85rem 1rem; gap: .7rem; }
            .lb-rank { width: 28px; }
            .lb-name { font-size: .9rem; }
            .lb-score-num { font-size: 1.1rem; }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4 lb-head">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-trophy-fill me-2" style="color:#facc15;"></i>Leaderboard</h2>
                    <p class="mb-0">See who's solving the most, ranked by problems completed.</p>
                </div>

                <div class="btn-group timeframe-group" role="group">
                    <input type="radio" class="btn-check" wire:model.live="timeframe" id="tf-weekly" value="weekly">
                    <label class="btn btn-sm" for="tf-weekly">Weekly</label>

                    <input type="radio" class="btn-check" wire:model.live="timeframe" id="tf-monthly" value="monthly">
                    <label class="btn btn-sm" for="tf-monthly">Monthly</label>

                    <input type="radio" class="btn-check" wire:model.live="timeframe" id="tf-all" value="all_time">
                    <label class="btn btn-sm" for="tf-all">All time</label>
                </div>
            </div>

            <div class="lb-panel">
                @forelse($leaders as $index => $leader)
                    <div class="lb-row {{ $index < 3 ? 'top3' : '' }}">
                        <div class="lb-rank">
                            @if($index == 0)
                                <i class="bi bi-award-fill rank-gold"></i>
                            @elseif($index == 1)
                                <i class="bi bi-award-fill rank-silver"></i>
                            @elseif($index == 2)
                                <i class="bi bi-award-fill rank-bronze"></i>
                            @else
                                #{{ $index + 1 }}
                            @endif
                        </div>

                        <div class="lb-avatar">{{ strtoupper(substr($leader->name, 0, 1)) }}</div>

                        <div class="flex-grow-1">
                            <div class="lb-name">
                                {{ $leader->name }}
                                @if($leader->id === auth()->id())
                                    <span class="badge you-badge">You</span>
                                @endif
                            </div>
                        </div>

                        <div class="lb-score">
                            <div class="lb-score-num">{{ $leader->solved_count }}</div>
                            <div class="lb-score-label">solved</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-mini">
                        <i class="bi bi-inbox"></i>
                        No data available for this timeframe.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>