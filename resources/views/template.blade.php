<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'wasteCare Web-GIS')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

        :root {
            color-scheme: light;
            --eco-primary: #10b981;
            --eco-primary-hover: #059669;
            --eco-primary-light: rgba(16, 185, 129, 0.08);
            --eco-secondary: #0f172a;
            --eco-surface: #ffffff;
            --eco-soft: #f8fafc;
            --eco-muted: #64748b;
            --eco-accent: #3b82f6;
            --eco-card-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(15, 23, 42, 0.03);
            --eco-hover-shadow: 0 20px 40px -15px rgba(16, 185, 129, 0.15), 0 1px 3px rgba(15, 23, 42, 0.04);
            --eco-border: rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.06) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.03) 0%, transparent 40%),
                        #f8fafc;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--eco-secondary);
            margin: 0;
            scroll-behavior: smooth;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Glassmorphism Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1100;
            background: #0f172a !important;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.85rem 0;
            transition: all 0.3s ease;
        }

        .navbar .nav-link,
        .navbar-brand {
            color: #f8fafc !important;
        }

        .navbar-brand {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            display: inline-flex;
            align-items: center;
        }

        .navbar .navbar-nav {
            align-items: center;
        }

        .navbar-toggler {
            border: 1px solid rgba(248, 250, 252, 0.25);
            box-shadow: none;
        }

        .navbar-toggler-icon {
            filter: invert(1);
        }

        .navbar .navbar-collapse {
            justify-content: flex-end;
        }

        main {
            margin-top: 80px;
        }

        /* Iconic brand font styling */
        .brand-logo-text {
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            letter-spacing: -0.05em;
            font-size: 1.45rem;
            display: inline-flex;
            align-items: center;
        }

        .brand-logo-text .text-waste {
            color: #ffffff;
            font-weight: 800;
        }

        .brand-logo-text .text-care {
            color: #10b981;
            font-weight: 400;
        }

        /* Brand/logo styles (circular leaf mark) */
        .brand {
            display: inline-flex;
            align-items: center;
        }

        .brand-logo-svg {
            border-radius: 50%;
            background: linear-gradient(180deg, rgba(255,255,255,0.03), transparent);
            box-shadow: 0 6px 18px -8px rgba(16,185,129,0.2), 0 2px 6px rgba(15,23,42,0.05);
            padding: 5px;
            width: 52px;
            height: 52px;
            min-width: 52px;
            min-height: 52px;
            flex-shrink: 0;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
            margin-left: 0.75rem;
        }

        .brand-logo-text {
            font-size: 1.2rem;
            letter-spacing: -0.02em;
        }

        .text-waste {
            color: #ffffff;
            font-weight: 800;
            text-transform: lowercase;
        }

        .text-care {
            color: #10b981;
            margin-left: 3px;
            font-weight: 700;
            text-transform: none;
        }

        .brand-tagline {
            font-size: 0.675rem;
            color: rgba(255,255,255,0.75);
            margin-top: 3px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .navbar .nav-link,
        .navbar .btn {
            border-radius: 10px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar .nav-link {
            color: rgba(248, 250, 252, 0.85) !important;
            background: transparent;
            border: 1px solid transparent;
            padding: 0.5rem 1rem;
            margin-right: 0.25rem;
            font-size: 0.9rem;
            font-weight: 500;
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link:focus {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        .navbar .btn-outline-light {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
            background: transparent;
        }

        .navbar .btn-outline-light:hover,
        .navbar .btn-outline-light:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .navbar .btn-login {
            color: #ffffff !important;
            background: var(--eco-primary);
            border: 1px solid var(--eco-primary);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .navbar .btn-login:hover,
        .navbar .btn-login:focus {
            background: var(--eco-primary-hover);
            border-color: var(--eco-primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        }

        .navbar .btn-logout {
            color: #ffffff !important;
            background: #ef4444;
            border: 1px solid #ef4444;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .navbar .btn-logout:hover,
        .navbar .btn-logout:focus {
            background: #dc2626;
            border-color: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.3);
        }

        .navbar .btn-dark {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            font-weight: 600;
        }

        .navbar .btn-dark:hover,
        .navbar .btn-dark:focus {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        .map-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: var(--eco-primary) !important;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .map-button:hover,
        .map-button:focus {
            background: var(--eco-primary);
            border-color: var(--eco-primary);
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .footer-base {
            background: linear-gradient(180deg, rgba(15,23,42,0.98), rgba(11,18,28,0.98));
            border-top: 1px solid rgba(255,255,255,0.03);
            padding: 0.9rem 0;
            color: rgba(248,250,252,0.8);
            margin-top: auto;
            box-shadow: 0 -8px 30px rgba(2,6,23,0.25);
        }

        /* Leaflet Popups Revamp */
        .leaflet-popup-content-wrapper {
            border-radius: 18px;
            border: 1px solid var(--eco-border);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.15);
            padding: 8px;
        }

        .leaflet-popup-content {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            margin: 8px 12px;
            line-height: 1.5;
        }

        .leaflet-popup-tip {
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.15);
        }

        /* Modernized Buttons styling */
        .btn-success {
            background-color: var(--eco-primary) !important;
            border-color: var(--eco-primary) !important;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-success:hover, .btn-success:focus {
            background-color: var(--eco-primary-hover) !important;
            border-color: var(--eco-primary-hover) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .btn-outline-success {
            color: var(--eco-primary) !important;
            border-color: var(--eco-primary) !important;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-outline-success:hover, .btn-outline-success:focus {
            background-color: var(--eco-primary) !important;
            border-color: var(--eco-primary) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .navbar .nav-link:last-child,
        .navbar .btn:last-child {
            margin-right: 0;
        }
    </style>

    @yield('styles')
</head>

<body>

    @include('navbar')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @unless(request()->routeIs('map'))
        <footer class="footer-base">
            <div class="container text-center">
                <small>© 2026 wasteCare · Sistem Informasi Geografis untuk monitoring lingkungan.</small>
            </div>
        </footer>
    @endunless

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @yield('scripts')
</body>

</html>
