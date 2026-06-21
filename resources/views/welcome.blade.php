<x-app-layout>
    <style>
        .hero-section {
            position: relative;
            overflow: hidden;
            padding: 5.5rem 0 4rem;
        }
        @media (min-width: 992px) { .hero-section { padding: 7rem 0 5rem; } }

        .hero-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            opacity: .35;
            z-index: 0;
        }
        [data-theme="light"] .hero-glow { opacity: .22; }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-family: var(--font-mono);
            font-size: .75rem;
            letter-spacing: .05em;
            color: var(--cyan);
            background: var(--cyan-soft);
            border: 1px solid color-mix(in srgb, var(--cyan) 30%, transparent);
            padding: .4rem .9rem;
            border-radius: 999px;
        }
        .eyebrow .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--success); box-shadow: 0 0 0 3px var(--success-soft); }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 3.6rem);
            font-weight: 700;
            line-height: 1.08;
            letter-spacing: -0.03em;
            margin: 1.1rem 0 1.2rem;
        }
        .hero-title .grad {
            background: linear-gradient(100deg, var(--violet), var(--cyan));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-sub { font-size: 1.08rem; color: var(--text-muted); max-width: 480px; line-height: 1.6; }

        .stat-pill { font-family: var(--font-mono); font-size: .82rem; color: var(--text-muted); }
        .stat-pill b { color: var(--text); font-weight: 700; }

        /* --- Terminal signature element --- */
        .terminal {
            background: var(--surface-solid);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 24px 60px -20px #00000080, var(--shadow-glow);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .terminal-bar {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .7rem 1rem;
            background: var(--surface-2);
            border-bottom: 1px solid var(--border);
        }
        .terminal-dot { width: 10px; height: 10px; border-radius: 50%; }
        .terminal-title { margin-left: .5rem; font-family: var(--font-mono); font-size: .76rem; color: var(--text-faint); }

        .terminal-body { padding: 1.25rem 1.4rem 1.4rem; font-family: var(--font-mono); font-size: .88rem; min-height: 230px; }
        .terminal-line { color: var(--text-faint); margin-bottom: .15rem; }
        .terminal-prompt { color: var(--success); }
        .terminal-query { color: var(--text); }
        .kw { color: var(--violet); }
        .col { color: var(--cyan); }
        .str { color: var(--warning); }

        .typed-cursor { display: inline-block; width: 7px; height: 1.05em; background: var(--cyan); vertical-align: text-bottom; animation: blink 1s steps(1) infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        .result-table { width: 100%; margin-top: 1rem; border-collapse: collapse; opacity: 0; transform: translateY(6px); transition: opacity .5s ease, transform .5s ease; }
        .result-table.show { opacity: 1; transform: translateY(0); }
        .result-table th { text-align: left; font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; color: var(--text-faint); padding: .35rem .6rem; border-bottom: 1px solid var(--border); }
        .result-table td { padding: .4rem .6rem; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        .result-table tr:last-child td { border-bottom: none; }
        .result-table td.num { color: var(--cyan); }
        .result-table td.name { color: var(--text); }

        .result-meta { margin-top: .7rem; font-size: .74rem; color: var(--success); opacity: 0; transition: opacity .4s ease .3s; }
        .result-meta.show { opacity: 1; }

        /* --- Feature cards --- */
        .feature-card { padding: 1.9rem; height: 100%; }
        .feature-icon-wrap {
            width: 52px; height: 52px;
            border-radius: var(--radius-sm);
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 1.1rem;
        }
        .feature-card h5 { font-size: 1.08rem; margin-bottom: .5rem; }
        .feature-card p { font-size: .9rem; line-height: 1.6; margin: 0; }

        /* --- How it works --- */
        .how-section { padding: 5rem 0; background: var(--bg-elevated); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        .how-step { display: flex; align-items: flex-start; gap: 1.1rem; }
        .how-step-marker {
            font-family: var(--font-mono);
            font-size: .75rem;
            color: var(--violet);
            background: var(--violet-soft);
            border: 1px solid color-mix(in srgb, var(--violet) 35%, transparent);
            width: 34px; height: 34px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .how-step h4 { font-size: 1.15rem; margin-bottom: .4rem; }
        .how-step p { color: var(--text-muted); font-size: .94rem; line-height: 1.65; margin: 0; }
        .how-connector { width: 1px; background: var(--border); flex: 1; margin: .25rem 0 .25rem 17px; min-height: 2.5rem; }

        .mini-panel {
            background: var(--surface-solid);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1rem 1.1rem;
            font-family: var(--font-mono);
            font-size: .78rem;
            color: var(--text-muted);
        }
        .mini-panel .bi { color: var(--violet); }

        /* --- CTA band --- */
        .cta-band {
            margin: 5rem auto;
            max-width: 1100px;
            border-radius: var(--radius-lg);
            padding: 3rem 2rem;
            text-align: center;
            background:
                radial-gradient(ellipse 60% 100% at 50% 0%, var(--violet-glow), transparent 70%),
                var(--surface-solid);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .cta-band h2 { font-size: clamp(1.6rem, 3vw, 2.2rem); margin-bottom: .6rem; }
        .cta-band p { color: var(--text-muted); max-width: 480px; margin: 0 auto 1.6rem; }
    </style>

    <!-- ============ HERO ============ -->
    <section class="hero-section">
        <div class="hero-glow" style="width:480px;height:480px;background:var(--violet);top:-160px;left:-120px;"></div>
        <div class="hero-glow" style="width:380px;height:380px;background:var(--cyan);bottom:-140px;right:-100px;"></div>

        <div class="container position-relative">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="eyebrow"><span class="dot"></span> 300+ problems &middot; live SQLite sandbox</span>
                    <h1 class="hero-title">
                        Write real SQL.<br>
                        Get <span class="grad">instant results.</span>
                    </h1>
                    <p class="hero-sub">
                        SqlPlatform is a hands-on SQL practice ground: every query runs against an isolated database the moment you hit run. No setup, no guesswork — just you, the schema, and the result set.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{ route('problems.index') }}" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-play-fill me-1"></i> Start solving
                        </a>
                        <a href="{{ route('leaderboard') }}" class="btn btn-outline-primary btn-lg px-4">
                            View leaderboard
                        </a>
                    </div>
                    <div class="d-flex flex-wrap gap-4 mt-4 pt-2">
                        <span class="stat-pill"><b>12,400+</b> queries run today</span>
                        <span class="stat-pill"><b>3</b> difficulty tiers</span>
                        <span class="stat-pill"><b>0ms</b> setup time</span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="terminal" id="hero-terminal">
                        <div class="terminal-bar">
                            <span class="terminal-dot" style="background:#f87171;"></span>
                            <span class="terminal-dot" style="background:#fbbf24;"></span>
                            <span class="terminal-dot" style="background:#34d399;"></span>
                            <span class="terminal-title">query_editor.sql</span>
                        </div>
                        <div class="terminal-body">
                            <div class="terminal-line"><span class="terminal-prompt">&#8674;</span> running against <span class="col">orders</span>, <span class="col">customers</span></div>
                            <div class="terminal-query" style="margin-top:.6rem;" id="typed-query"></div>
                            <table class="result-table" id="result-table">
                                <thead>
                                    <tr><th>customer</th><th>orders</th><th>total_spent</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td class="name">Maria Chen</td><td class="num">14</td><td class="num">$2,840.00</td></tr>
                                    <tr><td class="name">Devon Brooks</td><td class="num">11</td><td class="num">$2,115.50</td></tr>
                                    <tr><td class="name">Aisha Patel</td><td class="num">9</td><td class="num">$1,962.25</td></tr>
                                </tbody>
                            </table>
                            <div class="result-meta" id="result-meta">&#10003; 3 rows returned in 4ms</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FEATURES ============ -->
    <div class="container py-5">
        <div class="text-center mb-5 mx-auto" style="max-width: 560px;">
            <h2 class="fw-bold mb-2">Built for how you actually learn SQL</h2>
            <p class="text-muted">No videos to sit through. Just problems, a real database, and immediate feedback.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card hover-lift">
                    <div class="feature-icon-wrap" style="background: var(--violet-soft);">
                        <i class="bi bi-box-seam fs-3" style="color: var(--violet);"></i>
                    </div>
                    <h5>Isolated sandbox</h5>
                    <p class="text-muted">Every submission runs in its own SQLite instance, pre-loaded with realistic mock data — nothing you run can affect anyone else.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card hover-lift">
                    <div class="feature-icon-wrap" style="background: var(--cyan-soft);">
                        <i class="bi bi-lightning-charge-fill fs-3" style="color: var(--cyan);"></i>
                    </div>
                    <h5>Instant execution</h5>
                    <p class="text-muted">Run your query and see the result set in milliseconds. Compare it against expected output before you submit.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card hover-lift">
                    <div class="feature-icon-wrap" style="background: var(--success-soft);">
                        <i class="bi bi-trophy fs-3" style="color: var(--success);"></i>
                    </div>
                    <h5>Global leaderboard</h5>
                    <p class="text-muted">Track problems solved, accuracy, and topic mastery. Climb the board weekly, monthly, or all-time.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ HOW IT WORKS ============ -->
    <div class="how-section">
        <div class="container">
            <h2 class="fw-bold text-center mb-5">From schema to solved</h2>
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="how-step mb-2">
                        <div class="how-step-marker font-mono">01</div>
                        <div>
                            <h4>Pick a problem</h4>
                            <p>Filter by topic and difficulty — from single-table SELECTs to multi-table joins, window functions, and subqueries.</p>
                        </div>
                    </div>
                    <div class="how-connector"></div>

                    <div class="how-step mb-2">
                        <div class="how-step-marker font-mono">02</div>
                        <div>
                            <h4>Write and run</h4>
                            <p>Use the built-in editor to write your query and execute it instantly against the problem's isolated dataset.</p>
                            <div class="mini-panel mt-3 d-inline-block">
                                <i class="bi bi-terminal-fill me-1"></i> <span class="col" style="color:var(--cyan)">SELECT</span> * <span class="col" style="color:var(--cyan)">FROM</span> orders <span class="col" style="color:var(--cyan)">WHERE</span> status = <span style="color:var(--warning)">'shipped'</span>;
                            </div>
                        </div>
                    </div>
                    <div class="how-connector"></div>

                    <div class="how-step">
                        <div class="how-step-marker font-mono">03</div>
                        <div>
                            <h4>Submit and climb</h4>
                            <p>Once your output matches, the problem is marked solved, your stats update, and your spot on the leaderboard moves.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ============ CTA ============ -->
    <div class="container">
        <div class="cta-band">
            <h2>Your next query is one click away</h2>
            <p>Jump into a problem and run your first query in the sandbox right now.</p>
            <a href="{{ route('problems.index') }}" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-play-fill me-1"></i> Browse problems
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('typed-query');
            const table = document.getElementById('result-table');
            const meta = document.getElementById('result-meta');
            if (!el) return;

            const parts = [
                { t: 'SELECT ', c: 'kw' },
                { t: 'c.name AS customer, COUNT(o.id) AS orders, SUM(o.total) AS total_spent\n', c: 'col' },
                { t: 'FROM ', c: 'kw' },
                { t: 'customers c\n', c: 'col' },
                { t: 'JOIN ', c: 'kw' },
                { t: 'orders o ', c: 'col' },
                { t: 'ON ', c: 'kw' },
                { t: 'o.customer_id = c.id\n', c: 'col' },
                { t: 'GROUP BY ', c: 'kw' },
                { t: 'c.name\n', c: 'col' },
                { t: 'ORDER BY ', c: 'kw' },
                { t: 'total_spent ', c: 'col' },
                { t: 'DESC', c: 'kw' },
                { t: ';', c: '' },
            ];

            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (reduceMotion) {
                el.innerHTML = parts.map(p => `<span class="${p.c}">${p.t.replace(/\n/g, '<br>')}</span>`).join('');
                table.classList.add('show');
                meta.classList.add('show');
                return;
            }

            let html = '';
            let pi = 0, ci = 0;
            el.innerHTML = '<span class="typed-cursor"></span>';

            function step() {
                if (pi >= parts.length) {
                    setTimeout(() => {
                        table.classList.add('show');
                        meta.classList.add('show');
                    }, 200);
                    return;
                }
                const part = parts[pi];
                if (ci < part.t.length) {
                    html += part.t[ci] === '\n' ? '<br>' : part.t[ci];
                    el.innerHTML = `<span>${html}</span><span class="typed-cursor"></span>`;
                    ci++;
                    setTimeout(step, 14);
                } else {
                    html = html.replace(/<span class="typed-cursor"><\/span>/, '');
                    // rebuild with proper coloring up to this point
                    let colored = '';
                    for (let i = 0; i <= pi; i++) {
                        const seg = i === pi ? part.t : parts[i].t;
                        colored += `<span class="${parts[i].c}">${parts[i].t.replace(/\n/g, '<br>')}</span>`;
                    }
                    el.innerHTML = colored + '<span class="typed-cursor"></span>';
                    pi++; ci = 0;
                    setTimeout(step, 40);
                }
            }
            setTimeout(step, 400);
        });
    </script>
</x-app-layout>