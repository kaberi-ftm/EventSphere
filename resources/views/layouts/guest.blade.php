<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'EventSphere') }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
        }

        .auth-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns:
                minmax(380px, 0.9fr)
                minmax(480px, 1.1fr);
            color: #172033;
            background: #f5f7fb;
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        .auth-side {
            position: relative;
            overflow: hidden;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
            background:
                radial-gradient(
                    circle at 20% 15%,
                    rgba(59,130,246,0.35),
                    transparent 22rem
                ),
                linear-gradient(
                    150deg,
                    #111827,
                    #14213d
                );
        }

        .auth-side::after {
            position: absolute;
            right: -140px;
            bottom: -150px;
            width: 390px;
            height: 390px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 50%;
            content: "";
        }

        .auth-brand {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            font-size: 23px;
            font-weight: 800;
            text-decoration: none;
        }

        .auth-brand-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: #ffffff;
            background: #2563eb;
            font-size: 20px;
        }

        .auth-message {
            position: relative;
            z-index: 2;
            max-width: 480px;
        }

        .auth-message small {
            color: #93c5fd;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .auth-message h1 {
            margin: 13px 0 0;
            font-size: clamp(35px, 4vw, 52px);
            line-height: 1.08;
            letter-spacing: -0.04em;
        }

        .auth-message p {
            margin: 19px 0 0;
            color: #aab7cb;
            font-size: 16px;
            line-height: 1.7;
        }

        .auth-points {
            margin-top: 28px;
            display: grid;
            gap: 12px;
        }

        .auth-point {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #dbe6f5;
            font-size: 14px;
        }

        .auth-check {
            width: 21px;
            height: 21px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #052e1d;
            background: #6ee7b7;
            font-size: 11px;
            font-weight: 900;
        }

        .auth-footer {
            position: relative;
            z-index: 2;
            color: #71809a;
            font-size: 12px;
        }

        .auth-main {
            padding: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-content {
            width: min(100%, 470px);
        }

        .auth-mobile-brand {
            display: none;
            margin-bottom: 27px;
            align-items: center;
            gap: 10px;
            color: #111827;
            font-size: 21px;
            font-weight: 800;
            text-decoration: none;
        }

        .auth-mobile-brand span {
            width: 37px;
            height: 37px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            color: #ffffff;
            background: #2563eb;
        }

        .auth-card {
            padding: 34px;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #ffffff;
            box-shadow:
                0 15px 45px rgba(15,23,42,0.08);
        }

        .auth-heading {
            margin-bottom: 25px;
        }

        .auth-heading h2 {
            margin: 0;
            color: #111827;
            font-size: 27px;
            letter-spacing: -0.025em;
        }

        .auth-heading p {
            margin: 8px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .auth-field {
            margin-bottom: 18px;
        }

        .auth-label {
            display: block;
            margin-bottom: 7px;
            color: #344054;
            font-size: 13px;
            font-weight: 700;
        }

        .auth-input {
            width: 100%;
            min-height: 46px;
            padding: 10px 13px;
            color: #172033;
            background: #ffffff;
            border: 1px solid #cfd8e5;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            transition:
                border-color 180ms ease,
                box-shadow 180ms ease;
        }

        .auth-input:focus {
            border-color: #2563eb;
            box-shadow:
                0 0 0 4px rgba(37,99,235,0.1);
        }

        .auth-error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 12px;
            font-weight: 600;
        }

        .auth-session {
            margin-bottom: 18px;
            padding: 11px 13px;
            color: #166534;
            background: #ecfdf3;
            border-radius: 9px;
            font-size: 13px;
        }

        .auth-options {
            margin: 5px 0 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .auth-remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 13px;
        }

        .auth-remember input {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
        }

        .auth-link {
            color: #2563eb;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .auth-button {
            width: 100%;
            min-height: 46px;
            padding: 10px 18px;
            border: 0;
            border-radius: 10px;
            color: #ffffff;
            background: #2563eb;
            box-shadow:
                0 9px 22px rgba(37,99,235,0.2);
            cursor: pointer;
            font-size: 14px;
            font-weight: 800;
            transition: 180ms ease;
        }

        .auth-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .auth-switch {
            margin-top: 21px;
            color: #64748b;
            text-align: center;
            font-size: 13px;
        }

        .auth-home {
            display: inline-block;
            margin-top: 18px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-home:hover {
            color: #2563eb;
        }

        @media (max-width: 900px) {
            .auth-page {
                grid-template-columns: 1fr;
            }

            .auth-side {
                display: none;
            }

            .auth-main {
                min-height: 100vh;
                padding: 25px 16px;
            }

            .auth-mobile-brand {
                display: inline-flex;
            }
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 25px 20px;
            }

            .auth-options {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="auth-page">

    <aside class="auth-side">

        <a href="{{ url('/') }}"
           class="auth-brand">
            <span class="auth-brand-icon">E</span>
            EventSphere
        </a>

        <div class="auth-message">
            <small>Smart Event Platform</small>

            <h1>
                Better event management starts here.
            </h1>

            <p>
                Manage clubs, events, participants,
                volunteers, budgets, sponsors and
                certificates from one organized system.
            </p>

            <div class="auth-points">
                <div class="auth-point">
                    <span class="auth-check">✓</span>
                    Secure role-based access
                </div>

                <div class="auth-point">
                    <span class="auth-check">✓</span>
                    Complete event operations
                </div>

                <div class="auth-point">
                    <span class="auth-check">✓</span>
                    Oracle-powered reporting
                </div>
            </div>
        </div>

        <div class="auth-footer">
            © {{ date('Y') }} EventSphere
        </div>

    </aside>

    <main class="auth-main">

        <div class="auth-content">

            <a href="{{ url('/') }}"
               class="auth-mobile-brand">
                <span>E</span>
                EventSphere
            </a>

            {{ $slot }}

            <a href="{{ url('/') }}"
               class="auth-home">
                ← Return to home
            </a>

        </div>

    </main>

</div>

</body>
</html>