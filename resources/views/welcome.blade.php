<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="EventSphere Smart Event and Club Management Platform">

    <title>EventSphere</title>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-soft: #eff6ff;
            --navy: #111827;
            --text: #172033;
            --muted: #64748b;
            --background: #f7f9fc;
            --white: #ffffff;
            --border: #e2e8f0;
            --success: #16a34a;
            --radius: 16px;
            --shadow: 0 15px 40px rgba(15, 23, 42, 0.09);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            color: var(--text);
            background: var(--background);
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            line-height: 1.6;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: min(1120px, calc(100% - 36px));
            margin: 0 auto;
        }

        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
        }

        .nav-content {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            color: var(--navy);
            font-size: 22px;
            font-weight: 800;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: var(--white);
            background: var(--primary);
            font-size: 19px;
            font-weight: 800;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 27px;
        }

        .nav-link {
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .button {
            min-height: 42px;
            padding: 9px 17px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            transition: 180ms ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-outline {
            color: var(--primary);
            background: var(--white);
            border-color: #bfdbfe;
        }

        .button-primary {
            color: var(--white);
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
        }

        .button-primary:hover {
            background: var(--primary-dark);
        }

        .hero {
            padding: 95px 0 85px;
            background:
                radial-gradient(
                    circle at 90% 10%,
                    rgba(37, 99, 235, 0.09),
                    transparent 28rem
                ),
                var(--background);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            align-items: center;
            gap: 65px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 19px;
            padding: 7px 12px;
            color: var(--primary);
            background: var(--primary-soft);
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary);
        }

        .hero h1 {
            max-width: 630px;
            color: var(--navy);
            font-size: clamp(39px, 5vw, 60px);
            line-height: 1.08;
            letter-spacing: -0.045em;
        }

        .hero h1 span {
            color: var(--primary);
        }

        .hero-description {
            max-width: 620px;
            margin-top: 21px;
            color: var(--muted);
            font-size: 17px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .hero-points {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 27px;
            color: var(--muted);
            font-size: 13px;
        }

        .hero-point {
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .check-icon {
            width: 19px;
            height: 19px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--white);
            background: var(--success);
            font-size: 11px;
            font-weight: 800;
        }

        .hero-panel {
            padding: 28px;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: var(--white);
            box-shadow: var(--shadow);
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .panel-title h3 {
            color: var(--navy);
            font-size: 18px;
        }

        .panel-title p {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .status-badge {
            padding: 6px 10px;
            color: #166534;
            background: #dcfce7;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 13px;
        }

        .metric {
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fbfcfe;
        }

        .metric-label {
            color: var(--muted);
            font-size: 12px;
        }

        .metric-value {
            margin-top: 5px;
            color: var(--navy);
            font-size: 25px;
            font-weight: 800;
        }

        .progress-section {
            margin-top: 20px;
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .progress {
            height: 9px;
            overflow: hidden;
            border-radius: 999px;
            background: #e8edf5;
        }

        .progress-value {
            display: block;
            width: 76%;
            height: 100%;
            border-radius: inherit;
            background: var(--primary);
        }

        .features {
            padding: 80px 0;
            background: var(--white);
        }

        .section-heading {
            max-width: 650px;
            margin: 0 auto 42px;
            text-align: center;
        }

        .section-heading small {
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .section-heading h2 {
            margin-top: 10px;
            color: var(--navy);
            font-size: clamp(29px, 4vw, 40px);
            letter-spacing: -0.035em;
        }

        .section-heading p {
            margin-top: 13px;
            color: var(--muted);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            padding: 27px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--white);
        }

        .feature-icon {
            width: 45px;
            height: 45px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: var(--primary);
            background: var(--primary-soft);
            font-size: 20px;
        }

        .feature-card h3 {
            margin-top: 17px;
            color: var(--navy);
            font-size: 17px;
        }

        .feature-card p {
            margin-top: 8px;
            color: var(--muted);
            font-size: 14px;
        }

        .cta-section {
            padding: 75px 0;
        }

        .cta {
            padding: 38px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            border-radius: 20px;
            color: var(--white);
            background: var(--navy);
        }

        .cta h2 {
            font-size: clamp(25px, 4vw, 35px);
            letter-spacing: -0.025em;
        }

        .cta p {
            margin-top: 8px;
            color: #aab5c8;
        }

        .footer {
            padding: 25px 0;
            color: var(--muted);
            background: var(--white);
            border-top: 1px solid var(--border);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            font-size: 13px;
        }

        .footer strong {
            color: var(--navy);
        }

        @media (max-width: 900px) {
            .nav-links {
                display: none;
            }

            .hero-grid {
                grid-template-columns: 1fr;
            }

            .hero-panel {
                max-width: 650px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .container {
                width: min(100% - 24px, 1120px);
            }

            .nav-content {
                min-height: 65px;
            }

            .brand {
                font-size: 19px;
            }

            .brand-icon {
                width: 36px;
                height: 36px;
            }

            .nav-actions .button-outline {
                display: none;
            }

            .hero {
                padding: 65px 0;
            }

            .hero h1 {
                font-size: 39px;
            }

            .hero-actions .button {
                width: 100%;
            }

            .metric-grid {
                grid-template-columns: 1fr;
            }

            .cta {
                flex-direction: column;
                align-items: flex-start;
                padding: 29px 23px;
            }

            .footer-content {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="container nav-content">

        <a href="{{ url('/') }}"
           class="brand">
            <span class="brand-icon">E</span>
            EventSphere
        </a>

        <div class="nav-links">
            <a href="#features"
               class="nav-link">
                Features
            </a>

            @if(\Illuminate\Support\Facades\Route::has(
                'certificates.verify.form'
            ))
                <a href="{{ route(
                    'certificates.verify.form'
                ) }}"
                   class="nav-link">
                    Verify Certificate
                </a>
            @endif
        </div>

        <div class="nav-actions">
            @auth
                @php
                    $dashboardRoute = match(
                        auth()->user()->role?->name
                    ) {
                        'admin' =>
                            'admin.dashboard',

                        'executive' =>
                            'executive.dashboard',

                        'volunteer' =>
                            'volunteer.dashboard',

                        default =>
                            'participant.dashboard',
                    };
                @endphp

                <a href="{{ route($dashboardRoute) }}"
                   class="button button-primary">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="button button-outline">
                    Log in
                </a>

                @if(\Illuminate\Support\Facades\Route::has(
                    'register'
                ))
                    <a href="{{ route('register') }}"
                       class="button button-primary">
                        Register
                    </a>
                @endif
            @endauth
        </div>

    </div>
</nav>

<header class="hero">
    <div class="container hero-grid">

        <div>
            <div class="eyebrow">
                <span class="eyebrow-dot"></span>
                Smart Event Management
            </div>

            <h1>
                Manage your events
                <span>from one place.</span>
            </h1>

            <p class="hero-description">
                EventSphere connects events, clubs, participants,
                volunteers, sponsors, budgets, payments and
                certificates through one organized platform.
            </p>

            <div class="hero-actions">
                @auth
                    <a href="{{ route($dashboardRoute) }}"
                       class="button button-primary">
                        Open Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="button button-primary">
                        Get Started
                    </a>

                    @if(\Illuminate\Support\Facades\Route::has(
                        'register'
                    ))
                        <a href="{{ route('register') }}"
                           class="button button-outline">
                            Create Account
                        </a>
                    @endif
                @endauth
            </div>

            <div class="hero-points">
                <span class="hero-point">
                    <span class="check-icon">✓</span>
                    Role-based access
                </span>

                <span class="hero-point">
                    <span class="check-icon">✓</span>
                    Oracle database
                </span>

                <span class="hero-point">
                    <span class="check-icon">✓</span>
                    Analytical reports
                </span>
            </div>
        </div>

        <div class="hero-panel">

            <div class="panel-header">
                <div class="panel-title">
                    <h3>Event Overview</h3>
                    <p>EventSphere management dashboard</p>
                </div>

                <span class="status-badge">
                    Active
                </span>
            </div>

            <div class="metric-grid">
                <div class="metric">
                    <div class="metric-label">
                        Events
                    </div>

                    <div class="metric-value">
                        05
                    </div>
                </div>

                <div class="metric">
                    <div class="metric-label">
                        Clubs
                    </div>

                    <div class="metric-value">
                        04
                    </div>
                </div>

                <div class="metric">
                    <div class="metric-label">
                        Participants
                    </div>

                    <div class="metric-value">
                        120+
                    </div>
                </div>

                <div class="metric">
                    <div class="metric-label">
                        Volunteers
                    </div>

                    <div class="metric-value">
                        24
                    </div>
                </div>
            </div>

            <div class="progress-section">
                <div class="progress-header">
                    <span>Event preparation</span>
                    <span>76%</span>
                </div>

                <div class="progress">
                    <span class="progress-value"></span>
                </div>
            </div>

        </div>

    </div>
</header>

<section class="features"
         id="features">

    <div class="container">

        <div class="section-heading">
            <small>Core Features</small>

            <h2>
                One system for complete event operations
            </h2>

            <p>
                Manage important event activities without
                switching between multiple systems.
            </p>
        </div>

        <div class="feature-grid">

            <article class="feature-card">
                <div class="feature-icon">◆</div>

                <h3>Events and Registrations</h3>

                <p>
                    Create events, assign venues, register
                    participants and track attendance.
                </p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">✓</div>

                <h3>Volunteers and Tasks</h3>

                <p>
                    Manage volunteers, approvals, assigned
                    responsibilities and task progress.
                </p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">৳</div>

                <h3>Finance and Reporting</h3>

                <p>
                    Monitor sponsors, budgets, payments,
                    certificates and analytical reports.
                </p>
            </article>

        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">

        <div class="cta">
            <div>
                <h2>Start managing events efficiently.</h2>

                <p>
                    Sign in to access your EventSphere dashboard.
                </p>
            </div>

            @auth
                <a href="{{ route($dashboardRoute) }}"
                   class="button button-primary">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="button button-primary">
                    Log in
                </a>
            @endauth
        </div>

    </div>
</section>

<footer class="footer">
    <div class="container footer-content">
        <div>
            <strong>EventSphere</strong>
            — Smart Event & Club Management Platform
        </div>

        <div>
            © {{ date('Y') }} EventSphere
        </div>
    </div>
</footer>

</body>
</html>