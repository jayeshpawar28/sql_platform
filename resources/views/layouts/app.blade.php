<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a0e17">

    <title>{{ config('app.name', 'SqlPlatform') }}</title>

    <!-- Bootstrap 5 (grid/utilities only — visual chrome is fully overridden below) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Fonts: Space Grotesk (display) / Inter (body) / JetBrains Mono (data & code) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* ---- Dark theme (primary identity) ---- */
            --bg: #0a0e17;
            --bg-elevated: #0d1320;
            --surface: #11162250;
            --surface-solid: #11161f;
            --surface-2: #161d2b;
            --border: #232b3da8;
            --border-strong: #2e3850;

            --violet: #8b5cf6;
            --violet-soft: #8b5cf61f;
            --violet-glow: #8b5cf64d;
            --cyan: #22d3ee;
            --cyan-soft: #22d3ee1f;

            --text: #e6e9f0;
            --text-muted: #8b93a7;
            --text-faint: #5b6478;

            --success: #34d399;
            --success-soft: #34d39920;
            --warning: #fbbf24;
            --warning-soft: #fbbf2420;
            --danger: #f87171;
            --danger-soft: #f8717120;

            --radius-sm: 8px;
            --radius: 14px;
            --radius-lg: 20px;
            --shadow-glow: 0 8px 32px -8px var(--violet-glow);

            --font-display: 'Space Grotesk', 'Inter', sans-serif;
            --font-body: 'Inter', -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', ui-monospace, monospace;
        }

        [data-theme="light"] {
            --bg: #f7f8fb;
            --bg-elevated: #ffffff;
            --surface: #ffffffcc;
            --surface-solid: #ffffff;
            --surface-2: #f1f2f7;
            --border: #e3e6ee;
            --border-strong: #d3d8e4;

            --violet: #7c3aed;
            --violet-soft: #7c3aed14;
            --violet-glow: #7c3aed26;
            --cyan: #0891b2;
            --cyan-soft: #0891b214;

            --text: #161a23;
            --text-muted: #5b6478;
            --text-faint: #9aa1b2;

            --success: #059669;
            --success-soft: #05966914;
            --warning: #b45309;
            --warning-soft: #b4530914;
            --danger: #dc2626;
            --danger-soft: #dc262614;

            --shadow-glow: 0 8px 32px -8px #7c3aed1f;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text);
            transition: background-color .25s ease, color .25s ease;
            min-height: 100vh;
            position: relative;
        }

        /* Ambient grid/schema texture behind everything */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 64px 64px;
            opacity: .35;
            mask-image: radial-gradient(ellipse 70% 50% at 50% 0%, black 0%, transparent 70%);
            -webkit-mask-image: radial-gradient(ellipse 70% 50% at 50% 0%, black 0%, transparent 70%);
        }

        [data-theme="light"] body::before { opacity: .5; }

        h1, h2, h3, h4, h5, h6 { font-family: var(--font-display); letter-spacing: -0.02em; }

        .font-mono { font-family: var(--font-mono) !important; }

        a { color: var(--violet); }
        a:hover { color: var(--cyan); }

        /* ---------- Sharp-but-soft radii everywhere ---------- */
        .rounded, .rounded-1, .rounded-2, .rounded-3, .rounded-4 { border-radius: var(--radius) !important; }
        .rounded-pill { border-radius: 999px !important; }
        .card { border-radius: var(--radius) !important; }
        .btn { border-radius: var(--radius-sm) !important; }
        .badge { border-radius: 6px !important; }
        .form-control, .form-select { border-radius: var(--radius-sm) !important; }

        /* ---------- Glass navbar ---------- */
        .glass-nav {
            backdrop-filter: blur(16px) saturate(140%);
            -webkit-backdrop-filter: blur(16px) saturate(140%);
            background-color: color-mix(in srgb, var(--bg) 72%, transparent) !important;
            border-bottom: 1px solid var(--border);
            z-index: 1030;
        }

        .navbar-brand {
            font-family: var(--font-display);
            font-weight: 700;
            letter-spacing: -0.03em;
            font-size: 1.3rem;
            color: var(--text) !important;
        }
        .navbar-brand .brand-accent { color: var(--violet); }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            font-size: .92rem;
            padding: .5rem .9rem !important;
            border-radius: var(--radius-sm);
            transition: color .15s ease, background-color .15s ease;
        }
        .nav-link:hover { color: var(--text) !important; background: var(--surface-2); }
        .nav-link.active {
            color: var(--text) !important;
            background: var(--violet-soft);
            font-weight: 600 !important;
        }

        /* ---------- Buttons ---------- */
        .btn-primary {
            background: linear-gradient(135deg, var(--violet), #6d28d9);
            border: none;
            font-weight: 600;
            box-shadow: 0 1px 0 0 #ffffff14 inset, 0 4px 14px -4px var(--violet-glow);
            transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        }
        .btn-primary:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
            box-shadow: 0 1px 0 0 #ffffff14 inset, 0 8px 20px -4px var(--violet-glow);
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-outline-primary {
            color: var(--text);
            border: 1px solid var(--border-strong);
            background: transparent;
            font-weight: 500;
        }
        .btn-outline-primary:hover {
            background: var(--violet-soft);
            border-color: var(--violet);
            color: var(--violet);
        }

        .btn-outline-secondary {
            color: var(--text-muted);
            border-color: var(--border-strong);
            background: var(--surface);
        }
        .btn-outline-secondary:hover { background: var(--surface-2); color: var(--text); border-color: var(--border-strong); }

        .btn-link { color: var(--text-muted); }
        .btn-link:hover { color: var(--violet); }

        /* ---------- Cards ---------- */
        .card {
            background: var(--surface-solid);
            border: 1px solid var(--border);
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .hover-lift { cursor: default; }
        .hover-lift:hover {
            border-color: var(--violet) !important;
            box-shadow: var(--shadow-glow);
            transform: translateY(-4px);
        }

        /* ---------- Forms ---------- */
        .form-control, .form-select {
            background: var(--surface-2);
            border: 1px solid var(--border-strong);
            color: var(--text);
        }
        .form-control:focus, .form-select:focus {
            background: var(--surface-2);
            border-color: var(--violet);
            color: var(--text);
            box-shadow: 0 0 0 3px var(--violet-soft);
        }
        .form-control::placeholder { color: var(--text-faint); }

        /* ---------- Tables ---------- */
        .table { --bs-table-bg: transparent; --bs-table-color: var(--text); --bs-table-border-color: var(--border); }
        .table-light th, .table thead th {
            background: var(--surface-2) !important;
            color: var(--text-muted) !important;
            border-bottom: 1px solid var(--border) !important;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 600;
        }
        .table-hover > tbody > tr:hover > * { background-color: var(--violet-soft) !important; color: var(--text); }
        .table > :not(caption) > * > * { border-bottom-width: 1px; }

        /* ---------- Badges ---------- */
        .badge { font-weight: 600; font-size: .72rem; letter-spacing: .02em; padding: .42em .7em; }
        .bg-success { background-color: var(--success) !important; color: #06241a; }
        .bg-warning { background-color: var(--warning) !important; color: #2b1a00; }
        .bg-danger { background-color: var(--danger) !important; }
        .bg-primary { background-color: var(--violet) !important; }
        .bg-info { background-color: var(--cyan) !important; color: #04222b; }

        /* ---------- Dropdown ---------- */
        .dropdown-menu {
            background: var(--surface-solid);
            border: 1px solid var(--border);
            border-radius: var(--radius) !important;
            box-shadow: 0 16px 40px -12px #00000066;
            padding: .4rem;
        }
        .dropdown-item { color: var(--text-muted); border-radius: var(--radius-sm); font-size: .9rem; padding: .55rem .8rem; }
        .dropdown-item:hover, .dropdown-item:focus { background: var(--violet-soft); color: var(--text); }
        .dropdown-item.text-danger:hover { background: var(--danger-soft); color: var(--danger) !important; }
        .dropdown-divider { border-color: var(--border); }

        /* ---------- Misc surfaces ---------- */
        .bg-body-tertiary { background-color: var(--bg-elevated) !important; }
        .border-top { border-color: var(--border) !important; }
        .border-bottom { border-color: var(--border) !important; }
        .text-muted { color: var(--text-muted) !important; }
        .text-body { color: var(--text) !important; }
        .shadow-sm { box-shadow: 0 1px 2px #00000026 !important; }

        /* ---------- Scrollbar ---------- */
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 999px; border: 2px solid var(--bg); }
        ::-webkit-scrollbar-thumb:hover { background: var(--violet); }

        /* ---------- Focus visibility (a11y) ---------- */
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, .nav-link:focus-visible {
            outline: 2px solid var(--violet);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .001ms !important; transition-duration: .001ms !important; }
        }

        /* ---------- Theme toggle ---------- */
        #theme-toggle {
            width: 38px; height: 38px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: var(--radius-sm) !important;
            background: var(--surface-2);
            border: 1px solid var(--border);
            transition: border-color .15s ease, transform .2s ease;
        }
        #theme-toggle:hover { border-color: var(--violet); transform: rotate(-8deg); }
        #theme-toggle i { font-size: 1.05rem; color: var(--text-muted); }

        /* ---------- Mobile ---------- */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: var(--surface-solid);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                margin-top: .75rem;
                padding: 1rem;
            }
            .navbar-nav { margin-bottom: .75rem; }
            .navbar-toggler { border: 1px solid var(--border-strong) !important; border-radius: var(--radius-sm) !important; padding: .4rem .6rem; }
            .navbar-toggler:focus { box-shadow: 0 0 0 3px var(--violet-soft); }
        }
    </style>

    @stack('styles')
    @livewireStyles
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg glass-nav sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <!-- Logo mark: bracketed query glyph -->
                <svg width="30" height="30" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="navLogoGrad" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-color="#8b5cf6"/>
                            <stop offset="1" stop-color="#22d3ee"/>
                        </linearGradient>
                    </defs>
                    <rect width="32" height="32" rx="8" fill="url(#navLogoGrad)" fill-opacity="0.14"/>
                    <path d="M11 9L6 16L11 23" stroke="url(#navLogoGrad)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 9L26 16L21 23" stroke="url(#navLogoGrad)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="16" cy="16" r="2.1" fill="url(#navLogoGrad)"/>
                </svg>
                Sql<span class="brand-accent">Platform</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                            </a>
                        </li>
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('problems.*') ? 'active' : '' }}" href="{{ route('problems.index') }}">
                            <i class="bi bi-braces me-1"></i> Problems
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}" href="{{ route('leaderboard') }}">
                            <i class="bi bi-trophy me-1"></i> Leaderboard
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn p-0 border-0" id="theme-toggle" title="Toggle theme">
                        <i class="bi bi-sun-fill" id="theme-icon"></i>
                    </button>

                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                <span class="d-inline-flex align-items-center justify-content-center font-mono fw-bold"
                                      style="width:26px;height:26px;border-radius:7px;background:var(--violet-soft);color:var(--violet);font-size:.75rem;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i> Log out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3">Log in</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-3">Sign up free</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 position-relative" style="z-index: 1;">
        {{ $slot }}
    </main>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container" style="z-index: 1080;"></div>

    <footer class="py-4 mt-auto border-top position-relative" style="z-index:1; background: var(--bg-elevated);">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <span class="d-flex align-items-center gap-2 font-mono small" style="color: var(--text-faint);">
                <i class="bi bi-terminal" style="color: var(--violet);"></i> SqlPlatform &copy; {{ date('Y') }}
            </span>
            <span class="small" style="color: var(--text-faint);">Practice SQL. Ship confidence.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('theme-toggle');
            const icon = document.getElementById('theme-icon');
            const html = document.documentElement;

            let theme = localStorage.getItem('theme') || 'dark';
            apply(theme);

            btn.addEventListener('click', () => {
                theme = theme === 'dark' ? 'light' : 'dark';
                apply(theme);
                localStorage.setItem('theme', theme);
            });

            function apply(t) {
                html.setAttribute('data-theme', t);
                html.setAttribute('data-bs-theme', t);
                if (t === 'dark') {
                    icon.className = 'bi bi-sun-fill';
                } else {
                    icon.className = 'bi bi-moon-stars-fill';
                }
            }
        });
    </script>

    @stack('scripts')
    @livewireScripts
</body>
</html>