<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <h2 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i> Leaderboard</h2>
                
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" wire:model.live="timeframe" id="tf-weekly" value="weekly">
                    <label class="btn btn-outline-primary btn-sm" for="tf-weekly">Weekly</label>

                    <input type="radio" class="btn-check" wire:model.live="timeframe" id="tf-monthly" value="monthly">
                    <label class="btn btn-outline-primary btn-sm" for="tf-monthly">Monthly</label>

                    <input type="radio" class="btn-check" wire:model.live="timeframe" id="tf-all" value="all_time">
                    <label class="btn btn-outline-primary btn-sm" for="tf-all">All Time</label>
                </div>
            </div>
            
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($leaders as $index => $leader)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 {{ $index < 3 ? 'bg-body-tertiary' : '' }}">
                                <div class="d-flex align-items-center">
                                    <div class="me-4 text-center" style="width: 30px;">
                                        @if($index == 0)
                                            <i class="bi bi-award-fill text-warning fs-3"></i>
                                        @elseif($index == 1)
                                            <i class="bi bi-award-fill text-secondary fs-3"></i>
                                        @elseif($index == 2)
                                            <i class="bi bi-award-fill fs-3" style="color: #cd7f32;"></i>
                                        @else
                                            <span class="fw-bold text-muted fs-5">#{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div class="fw-bold fs-5 {{ $index < 3 ? 'text-body' : 'text-secondary' }}">
                                        {{ $leader->name }}
                                        @if($leader->id === auth()->id())
                                            <span class="badge bg-primary ms-2 fs-6">You</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="fs-4 fw-bold text-success">{{ $leader->solved_count }}</span>
                                    <span class="text-muted d-block small mt-n1">Solved</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                No data available for this timeframe.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
