<nav class="navbar navbar-expand-lg navbar-dark" style="z-index: 1050;">
    <div class="container px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <div class="d-flex align-items-center brand">
                <svg class="brand-logo-svg" width="44" height="44" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                    <defs>
                        <linearGradient id="g1" x1="0%" x2="100%" y1="0%" y2="100%">
                            <stop offset="0%" stop-color="#6ee7b7"/>
                            <stop offset="100%" stop-color="#10b981"/>
                        </linearGradient>
                    </defs>
                    <circle cx="32" cy="32" r="30" fill="url(#g1)" />
                    <path d="M22.5 35c2-6 8-11 15-11 0 8-6 15-12 18-2-4-5-6-3-7z" fill="#ffffff" opacity="0.98"/>
                    <path d="M36 24c4 3 6 8 6 12-3-2-7-6-9-10 1-1 2-2 3-2z" fill="#ffffff" opacity="0.9"/>
                </svg>
                <div class="brand-text ms-2">
                    <div class="brand-logo-text"><span class="text-waste">waste</span><span class="text-care">Care</span></div>
                    <div class="brand-tagline">Smart Solutions</div>
                </div>
            </div>
            <span class="badge bg-success-subtle text-emerald-400 border border-success-subtle rounded-pill px-2.5 py-1 small ms-1" style="font-size: 0.7rem; font-weight: 600; color: #34d399; border-color: rgba(52, 211, 153, 0.2); background: rgba(52, 211, 153, 0.1);">Web-GIS</span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="topNavbar">
            @unless(request()->is('/'))
                <div class="ms-auto navbar-nav align-items-center gap-2 mt-3 mt-lg-0">
                    <a href="{{ route('map') }}" class="nav-link map-button text-white small px-3 py-2"><i class="fa-solid fa-map-location-dot me-1.5"></i> Peta Publik</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-dark btn-sm shadow-sm text-white px-3 py-2 nav-button"><i class="fa-solid fa-sliders me-1.5"></i> Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-logout btn-sm nav-button px-3 py-2"><i class="fa-solid fa-right-from-bracket me-1.5"></i> Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-login btn-sm nav-button px-3.5 py-2"><i class="fa-solid fa-arrow-right-to-bracket me-1.5"></i> Masuk Admin</a>
                    @endauth
                </div>
            @endunless
        </div>
    </div>
</nav>
