<div class="container py-4">
    <h2 class="mb-4 fw-bold">My Dashboard</h2>
    
    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center py-4 bg-primary text-white">
                <i class="bi bi-check-circle-fill fs-1 mb-2"></i>
                <h2 class="fw-bold mb-0">{{ $totalSolved }}</h2>
                <p class="mb-0 text-white-50">Problems Solved</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center py-4 bg-info text-white">
                <i class="bi bi-code-slash fs-1 mb-2"></i>
                <h2 class="fw-bold mb-0">{{ $totalSubmissions }}</h2>
                <p class="mb-0 text-white-50">Total Submissions</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center py-4 bg-success text-white">
                <i class="bi bi-bullseye fs-1 mb-2"></i>
                <h2 class="fw-bold mb-0">{{ $accuracy }}%</h2>
                <p class="mb-0 text-white-50">Accuracy Rate</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Topic Progress -->
         <!-- <button wire:click="toggleNotification" class="btn btn-primary">Click me</button> -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent pt-3 pb-2 border-bottom">
                    <h5 class="fw-bold mb-0">Topic Progress</h5>
                </div>
                <div class="card-body">
                    @forelse($topics as $topic)
                        @php
                            $percentage = $topic->total_problems > 0 ? ($topic->problems_solved / $topic->total_problems) * 100 : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold">{{ $topic->topic }}</span>
                                <span class="text-muted small">{{ $topic->problems_solved }} / {{ $topic->total_problems }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No progress tracked yet. Start solving problems!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="card shadow-sm border-0 mt-2">
        <div class="card-header bg-transparent pt-3 pb-2 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Recent Submissions</h5>
            <a href="{{ route('problems.index') }}" class="btn btn-sm btn-outline-primary">Solve More</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Problem</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubmissions as $submission)
                            <tr>
                                <td>
                                    <a href="{{ route('problems.show', $submission->problem) }}" class="text-decoration-none fw-bold">
                                        {{ $submission->problem->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($submission->status === 'correct')
                                        <span class="badge bg-success">Accepted</span>
                                    @elseif($submission->status === 'incorrect')
                                        <span class="badge bg-warning text-dark">Wrong Answer</span>
                                    @else
                                        <span class="badge bg-danger">Error</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $submission->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No recent submissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
