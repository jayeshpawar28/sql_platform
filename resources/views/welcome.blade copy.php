<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-dark text-white text-center py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
        <div class="container position-relative z-1 py-5">
            <h1 class="display-3 fw-bold mb-3" style="letter-spacing: -1px;">Master SQL Interactively.</h1>
            <p class="lead mb-4 text-white-50 mx-auto" style="max-width: 600px;">
                Practice writing complex queries in a safe, sandboxed environment. Get instant AI-powered feedback, climb the leaderboard, and earn badges.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('problems.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">Start Solving</a>
            </div>
        </div>
        
        <!-- Abstract Decorative Shapes -->
        <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-primary opacity-25 blur-3xl" style="width: 400px; height: 400px; filter: blur(60px);"></div>
        <div class="position-absolute bottom-0 end-0 translate-middle-y rounded-circle bg-info opacity-25 blur-3xl" style="width: 300px; height: 300px; filter: blur(60px);"></div>
    </div>

    <!-- Features Section -->
    <div class="container py-5 my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why SQLPlatform?</h2>
            <p class="text-muted">Everything you need to sharpen your database skills.</p>
        </div>
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 text-center hover-lift" style="transition: transform 0.3s; cursor: default;">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-box-seam fs-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Isolated Sandbox</h5>
                    <p class="text-muted small">Execute your queries safely. Every submission runs in an isolated SQLite database loaded with precise mock data.</p>
                </div>
            </div>
            <!-- Feature 2 -->
            <!-- <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 text-center hover-lift" style="transition: transform 0.3s; cursor: default;">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-robot fs-1 text-info"></i>
                    </div>
                    <h5 class="fw-bold">AI Companion</h5>
                    <p class="text-muted small">Stuck? Our GPT-4o-mini integration offers smart hints, explains optimal solutions, and analyzes your queries.</p>
                </div>
            </div> -->
            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4 text-center hover-lift" style="transition: transform 0.3s; cursor: default;">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-trophy fs-1 text-success"></i>
                    </div>
                    <h5 class="fw-bold">Global Leaderboard</h5>
                    <p class="text-muted small">Compete globally or track personal milestones. Watch your name rise on the leaderboard as you solve more problems.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How it Works -->
    <div class="bg-body-tertiary py-5 border-top border-bottom">
        <div class="container py-4">
            <h2 class="fw-bold text-center mb-5">How It Works</h2>
            
            <div class="row align-items-center mb-5">
                <div class="col-md-6 order-md-2">
                    <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=800" class="img-fluid rounded shadow-lg mb-4 mb-md-0" alt="Code Editor">
                </div>
                <div class="col-md-6 order-md-1 pe-md-5">
                    <h3 class="fw-bold text-primary mb-3">1. Choose a Problem</h3>
                    <p class="lead text-muted">Browse our extensive library covering everything from basic SELECTs to complex Window Functions and JOINs across multi-table schemas.</p>
                </div>
            </div>
            
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=800" class="img-fluid rounded shadow-lg mb-4 mb-md-0" alt="Data Analysis">
                </div>
                <div class="col-md-6 ps-md-5">
                    <h3 class="fw-bold text-info mb-3">2. Write & Test</h3>
                    <p class="lead text-muted">Use our integrated Monaco editor to craft your query. Run it instantly against our isolated environment to verify your output before submitting.</p>
                </div>
            </div>
            
            <div class="row align-items-center">
                <div class="col-md-6 order-md-2">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800" class="img-fluid rounded shadow-lg mb-4 mb-md-0" alt="Success">
                </div>
                <div class="col-md-6 order-md-1 pe-md-5">
                    <h3 class="fw-bold text-success mb-3">3. Learn & Climb</h3>
                    <p class="lead text-muted">Once solved, let the AI review your code. Learn optimizations and watch your name rise on the global leaderboard.</p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .hover-lift:hover { transform: translateY(-5px); }
    </style>
</x-app-layout>
