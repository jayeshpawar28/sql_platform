<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SQLPlatform') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: var(--bs-body-bg); color: var(--bs-body-color); transition: background-color 0.3s, color 0.3s; }
        .navbar-brand { font-weight: 800; letter-spacing: -0.5px; }
        .glass-nav { backdrop-filter: blur(10px); background-color: rgba(255, 255, 255, 0.8) !important; border-bottom: 1px solid rgba(0,0,0,0.05); }
        
        /* Midnight Cyber Pattern Variables */
        [data-bs-theme="dark"] {
            --bs-body-bg: #0d1117;
            --bs-body-color: #c9d1d9;
            --bs-secondary-color: #8b949e;
            --bs-tertiary-bg: #161b22;
            --bs-border-color: #30363d;
            --bs-primary: #58a6ff;
            --bs-info: #a371f7;
            --bs-success: #2ea043;
            --bs-light: #161b22;
        }
        [data-bs-theme="dark"] .glass-nav { background-color: rgba(13, 17, 23, 0.8) !important; border-bottom: 1px solid rgba(255,255,255,0.05); }
        
        /* Sharp Edges */
        .rounded, .rounded-1, .rounded-2, .rounded-3, .rounded-4, .rounded-pill, .rounded-circle, .card, .btn, .alert, .badge { 
            border-radius: 2px !important; 
        }
        
        /* Hover Glow Effects */
        [data-bs-theme="dark"] .hover-lift:hover { 
            border-color: rgba(88, 166, 255, 0.5) !important; 
            box-shadow: 0 0 15px rgba(88, 166, 255, 0.15) !important; 
        }
        [data-bs-theme="dark"] .card, [data-bs-theme="dark"] .border { 
            border-color: var(--bs-border-color) !important; 
        }
        
        /* Tables */
        [data-bs-theme="dark"] .table-light th { 
            background-color: var(--bs-tertiary-bg) !important; 
            color: var(--bs-body-color) !important; 
            border-bottom-color: var(--bs-border-color) !important;
        }
        [data-bs-theme="dark"] .table { --bs-table-bg: transparent; --bs-table-border-color: var(--bs-border-color); --bs-table-color: var(--bs-body-color); --bs-table-hover-color: var(--bs-body-color); }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--bs-body-bg); }
        ::-webkit-scrollbar-thumb { background: var(--bs-border-color); border-radius: 2px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--bs-secondary-color); }
    </style>
    
    @livewireStyles
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg glass-nav sticky-top">
        <div class="container">
            <a class="navbar-brand text-primary d-flex align-items-center" href="{{ route('home') }}">
                <i class="bi bi-database-fill-gear fs-3 me-2"></i> SQLPlatform
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('problems.*') ? 'active fw-bold' : '' }}" href="{{ route('problems.index') }}">Problems</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('leaderboard') ? 'active fw-bold' : '' }}" href="{{ route('leaderboard') }}">Leaderboard</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-link text-body p-0 border-0" id="theme-toggle" title="Toggle Dark Mode">
                        <i class="bi bi-moon-stars-fill fs-5" id="theme-icon"></i>
                    </button>
                    
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <!-- <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li> -->
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Log Out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">Log in</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        {{ $slot }}
    </main>
    
    <!-- Toast Container for Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container"></div>

    <footer class="py-4 bg-body-tertiary mt-auto border-top">
        <div class="container text-center">
            <span class="text-muted small">&copy; {{ date('Y') }} SQLPlatform. Master SQL dynamically.</span>
        </div>
        
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dark Mode Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const htmlElement = document.documentElement;
            
            // Check local storage or system preference
            let currentTheme = localStorage.getItem('theme');
            if(!currentTheme) {
                currentTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            
            applyTheme(currentTheme);

            themeToggleBtn.addEventListener('click', () => {
                currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(currentTheme);
                localStorage.setItem('theme', currentTheme);
            });

            function applyTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                if(theme === 'dark') {
                    themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
                    themeIcon.classList.add('text-warning');
                } else {
                    themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
                    themeIcon.classList.remove('text-warning');
                }
            }
        });
    </script>
    
    @livewireScripts
</body>
</html>
