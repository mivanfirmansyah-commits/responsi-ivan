@extends('template')

@section('title', 'Peta Pemantauan & Manajemen Spasial - wasteCare')

@section('styles')
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            background: #eef2f7;
        }

        main {
            flex-grow: 1;
            min-height: calc(100vh - 80px);
            height: calc(100vh - 80px);
            overflow: hidden;
        }

        .main-container {
            display: flex;
            position: absolute;
            top: 80px;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            background: #eef2f7;
            width: 100%;
            padding-top: 12px;
        }

        #map {
            min-height: 100%;
        }

        /* Floating Sidebar Design - Mapbox Aesthetic */
        .sidebar-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            bottom: 20px;
            width: 380px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12), 0 1px 3px rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-content {
            flex-grow: 1;
            padding: 24px;
            overflow-y: auto;
        }

        /* Sidebar Hidden state using Translate */
        .sidebar-panel.sidebar-hidden {
            transform: translateX(-420px);
        }

        .sidebar-header {
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            margin-bottom: 20px;
        }

        .sidebar-title {
            font-size: 1.15rem;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .sidebar-subtitle {
            color: #64748b;
            line-height: 1.5;
            font-size: 0.85rem;
        }

        .sidebar-notice {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.15);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #065f46;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .btn-geolocate {
            min-width: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .btn-geolocate:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Custom Tab Pill styling */
        .nav-tabs-custom {
            display: flex;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
            border: none;
            margin-bottom: 1rem;
            gap: 2px;
        }

        .nav-tabs-custom .nav-item {
            flex: 1;
            display: flex;
        }

        .nav-tabs-custom .nav-link {
            width: 100%;
            text-align: center;
            border-radius: 8px;
            border: none;
            padding: 0.55rem 0.4rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            background: transparent;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .nav-tabs-custom .nav-link:hover {
            color: #0f172a;
            background: rgba(15, 23, 42, 0.03);
        }

        .nav-tabs-custom .nav-link.active {
            color: #ffffff;
            background: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .sidebar-control-card {
            background: #f8fafc;
            border: 1px solid rgba(15, 23, 42, 0.05);
            border-radius: 16px;
            padding: 1.15rem;
            margin-bottom: 1.25rem;
        }

        .form-control {
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-radius: 10px;
            padding: 0.55rem 0.75rem;
            font-size: 0.85rem;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .form-control-file {
            border-radius: 10px;
        }

        .card.card-body {
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, 0.06);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02) !important;
        }

        .section-label {
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            color: #475569;
            margin-bottom: 0.35rem;
            display: inline-block;
        }

        .info-tag {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 0.75rem;
            display: block;
            line-height: 1.5;
        }

        #map {
            flex-grow: 1;
            height: 100%;
            width: 100%;
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 66px);
        }

        /* Modernized Sidebar Toggle Button */
        .sidebar-toggle-btn {
            position: absolute;
            top: 24px;
            right: -65px;
            z-index: 1005;
            background: #ffffff;
            border: 1px solid rgba(16, 185, 129, 0.35);
            border-radius: 16px;
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 18px 35px rgba(15, 23, 42, 0.18);
            font-size: 20px;
            color: #10b981;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        .sidebar-toggle-btn:hover {
            background-color: #10b981;
            color: #ffffff;
            transform: translateX(0) scale(1.05);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.25);
        }

        .sidebar-toggle-btn:active {
            transform: translateX(0) scale(0.98);
        }

        .footer-note {
            font-size: 0.75rem;
            color: #94a3b8;
            text-align: center;
            margin-top: 15px;
            line-height: 1.4;
        }

        /* Leaflet Controls Styling override */
        .leaflet-bar {
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.1) !important;
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            border-radius: 10px !important;
            overflow: hidden;
        }

        .leaflet-bar a {
            background: #ffffff !important;
            color: #0f172a !important;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08) !important;
            transition: all 0.2s ease;
        }

        .leaflet-bar a:hover {
            background: #f8fafc !important;
            color: #10b981 !important;
        }

        /* Drawing cursor */
        .drawing-mode {
            cursor: crosshair !important;
        }

        /* Leaflet popup styling overrides inside map view */
        .popup-card {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .popup-title {
            margin: 0 0 0.4rem 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .popup-subtitle {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: #475569;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .popup-subtitle .badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-weight: 600;
        }

        .popup-text {
            margin: 0 0 0.75rem 0;
            line-height: 1.5;
            color: #475569;
            font-size: 0.85rem;
        }

        .popup-image {
            display: block;
            width: 100%;
            border-radius: 10px;
            margin-top: 0.65rem;
            object-fit: cover;
            max-height: 140px;
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .popup-meta {
            display: grid;
            gap: 0.3rem;
            font-size: 0.78rem;
            color: #64748b;
            border-top: 1px solid rgba(15, 23, 42, 0.06);
            padding-top: 0.65rem;
            margin-top: 0.65rem;
        }

        .popup-meta strong {
            color: #0f172a;
        }

        /* Responsivitas Layout Peta */
        @media (max-width: 768px) {
            .sidebar-panel {
                left: 10px;
                top: 10px;
                bottom: 10px;
                width: calc(100% - 20px);
                max-width: 350px;
            }
            .sidebar-panel.sidebar-hidden {
                transform: translateX(-370px);
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-container">
        <div id="sidebarPanel" class="sidebar-panel">
            <div id="toggleSidebar" class="sidebar-toggle-btn" title="Buka/Tutup Sidebar">☰</div>

            <div class="sidebar-content">
                <div class="sidebar-header">
                    <h4 class="fw-bold sidebar-title" style="font-family: 'Outfit', sans-serif; font-weight: 800; letter-spacing: -0.03em;">wasteCare Spatial Control</h4>
                    <p class="sidebar-subtitle mb-0">Kelola laporan sampah, rute truk, dan zona kawasan secara cepat dari satu panel.</p>
                </div>

                @if (session('success'))
                    <div class="sidebar-notice">⚡ {{ session('success') }}</div>
                @endif

                <div class="sidebar-control-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="text-uppercase text-secondary" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Mode Input</div>
                            <strong class="text-dark" style="font-size: 0.88rem;">Pilih tindakan spasial</strong>
                        </div>
                        <span class="badge bg-success rounded-pill px-2.5 py-1" style="font-size: 0.7rem; font-weight: 700;">Live</span>
                    </div>

                    <ul class="nav nav-tabs nav-tabs-custom mb-0" id="spatialTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="point-tab" data-bs-toggle="tab" data-bs-target="#panel-point"
                                type="button" onclick="switchMode('point')">📍 Lapor</button>
                        </li>
                        @auth
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="line-tab" data-bs-toggle="tab" data-bs-target="#panel-line"
                                    type="button" onclick="switchMode('line')">🛣️ Rute</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="polygon-tab" data-bs-toggle="tab" data-bs-target="#panel-polygon"
                                    type="button" onclick="switchMode('polygon')">🟩 Zona</button>
                            </li>
                        @endauth
                    </ul>
                </div>

                <div class="tab-content" id="spatialTabContent">
                    <div class="tab-pane fade show active" id="panel-point" role="tabpanel">
                        <div class="sidebar-control-card">
                            <p class="info-tag mb-3">Klik tombol di bawah lalu klik satu titik di peta untuk menandai lokasi tumpukan sampah.</p>
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" id="btn-draw-point"
                                    class="btn btn-outline-success w-100 btn-sm fw-bold py-2">➕ Tentukan Lokasi Titik</button>
                                <button type="button" id="btn-geolocate" class="btn btn-success btn-sm fw-bold py-2" title="Gunakan lokasi GPS perangkat">📍 Lokasi Saya</button>
                            </div>
                            <form action="{{ route('points.store') }}" method="POST" enctype="multipart/form-data"
                                class="card card-body bg-white border-0 p-3 shadow-sm d-none" id="form-point">
                                @csrf
                                <div class="row g-2 mb-3">
                                    <div class="col"><input type="text" id="lat-point" name="latitude"
                                            class="form-control form-control-sm bg-white" placeholder="Latitude" readonly
                                            required style="font-family: monospace;"></div>
                                    <div class="col"><input type="text" id="lng-point" name="longitude"
                                            class="form-control form-control-sm bg-white" placeholder="Longitude" readonly
                                            required style="font-family: monospace;"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="section-label">Pelapor / Lokasi</label>
                                    <input type="text" name="name" class="form-control form-control-sm" required placeholder="Contoh: Depan Komplek A">
                                </div>
                                <div class="mb-3">
                                    <label class="section-label">Deskripsi Masalah</label>
                                    <textarea name="description" class="form-control form-control-sm" rows="2" required placeholder="Contoh: Tumpukan daun basah belum diangkut"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="section-label">Foto Bukti</label>
                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold py-2">🚀 Kirim Laporan Sampah</button>
                            </form>
                        </div>
                    </div>

                    @auth
                        <div class="tab-pane fade" id="panel-line" role="tabpanel">
                            <div class="sidebar-control-card">
                                <p class="info-tag mb-3">Gambarkan rute dengan beberapa klik berurutan pada peta, lalu lengkapi info jadwal rute.</p>
                                <button type="button" id="btn-draw-line"
                                    class="btn btn-outline-success w-100 btn-sm fw-bold py-2 mb-2">🛣️ Mulai Gambar Jalur Truk</button>
                                <button type="button" id="btn-reset-line"
                                    class="btn btn-link text-danger w-100 small d-none mb-3 text-decoration-none">Hapus Titik Garis Terakhir</button>

                                <form action="{{ route('routes.store') }}" method="POST"
                                    class="card card-body bg-white border-0 p-3 shadow-sm d-none" id="form-line">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="section-label">Nama / Kode Rute</label>
                                        <input type="text" name="name" class="form-control form-control-sm" required
                                            placeholder="Contoh: Rute Utama">
                                    </div>
                                    <div class="mb-3">
                                        <label class="section-label">Jadwal Pengangkutan</label>
                                        <input type="text" name="schedule" class="form-control form-control-sm" required
                                            placeholder="Contoh: Senin & Kamis (08:00)">
                                    </div>
                                    <div class="mb-3">
                                        <label class="section-label">Koordinat Jalur (JSON)</label>
                                        <textarea id="coords-line" name="coordinates" class="form-control form-control-sm bg-white" rows="2" readonly
                                            required placeholder="Klik peta berurutan..." style="font-family: monospace; font-size: 0.75rem;"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm w-100 fw-bold py-2">🛣️ Simpan Rute</button>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="panel-polygon" role="tabpanel">
                            <div class="sidebar-control-card">
                                <p class="info-tag mb-3">Gambarkan area zona dengan minimal 3 klik pada peta untuk membentuk batas wilayah.</p>
                                <button type="button" id="btn-draw-polygon"
                                    class="btn btn-outline-success w-100 btn-sm fw-bold py-2 mb-2">🟩 Mulai Gambar Batas Wilayah</button>
                                <button type="button" id="btn-reset-polygon"
                                    class="btn btn-link text-danger w-100 small d-none mb-3 text-decoration-none">Hapus Titik Sudut Terakhir</button>

                                <form action="{{ route('zones.store') }}" method="POST"
                                    class="card card-body bg-white border-0 p-3 shadow-sm d-none" id="form-polygon">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="section-label">Nama Wilayah</label>
                                        <input type="text" name="zone_name" class="form-control form-control-sm" required
                                            placeholder="Contoh: Wilayah RT 01">
                                    </div>
                                    <div class="mb-3">
                                        <label class="section-label">Estimasi Luas Area (m²)</label>
                                        <input type="number" name="area_size" class="form-control form-control-sm" required
                                            placeholder="Contoh: 1500">
                                    </div>
                                    <div class="mb-3">
                                        <label class="section-label">Koordinat Batas (JSON)</label>
                                        <textarea id="coords-polygon" name="coordinates" class="form-control form-control-sm bg-white" rows="2"
                                            readonly required placeholder="Klik minimal 3 lokasi..." style="font-family: monospace; font-size: 0.75rem;"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm w-100 fw-bold py-2">🟩 Simpan Zona</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>

                <div class="footer-note">
                    wasteCare Spatial Dashboard • Pantau perubahan secara real-time dan simpan data lokasi dengan mudah.
                </div>
            </div>
        </div>

        <div id="map"></div>
    </div>
@endsection

@section('scripts')
    <script>
        // Inisialisasi Utama Peta Leaflet
        var map = L.map('map', {
            zoomControl: false
        }).setView([-7.8256, 110.3630], 16);

        L.control.zoom({
            position: 'topright'
        }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Kontrol Sidebar Animasi Slide
        const toggleSidebarBtn = document.getElementById('toggleSidebar');
        const sidebarPanel = document.getElementById('sidebarPanel');
        if (toggleSidebarBtn && sidebarPanel) {
            toggleSidebarBtn.addEventListener('click', function() {
                sidebarPanel.classList.toggle('sidebar-hidden');
                setTimeout(() => {
                    map.invalidateSize();
                }, 300);
            });
        }

        // STATE VARIABLE UNTUK GAMBAR SPASIAL
        let isDrawing = false;
        let currentActiveMode = 'point';
        let tempPointsArray = [];

        // Objek Gambar Sementara di Layar Peta
        let tempMarker = null;
        let tempPolyline = null;
        let tempPolygon = null;

        const mapContainer = document.getElementById('map');

        function switchMode(mode) {
            currentActiveMode = mode;
        }

        function resetDrawingState() {
            isDrawing = false;
            if (mapContainer) mapContainer.classList.remove('drawing-mode');
            tempPointsArray = [];

            if (tempMarker) {
                map.removeLayer(tempMarker);
                tempMarker = null;
            }
            if (tempPolyline) {
                map.removeLayer(tempPolyline);
                tempPolyline = null;
            }
            if (tempPolygon) {
                map.removeLayer(tempPolygon);
                tempPolygon = null;
            }

            // Proteksi elemen Point
            const btnDrawPoint = document.getElementById('btn-draw-point');
            const formPoint = document.getElementById('form-point');
            if (btnDrawPoint) {
                btnDrawPoint.className = "btn btn-outline-success w-100 btn-sm fw-bold py-2 mb-3";
                btnDrawPoint.innerHTML = "➕ Tentukan Lokasi Titik";
            }
            if (formPoint) formPoint.classList.add('d-none');

            // --- PROTEKSI ELEMEN ADMIN (MENGGUNAKAN IF CHECK AGAR TIDAK EROR DI USER PUBLIK) ---
            const btnDrawLine = document.getElementById('btn-draw-line');
            const btnResetLine = document.getElementById('btn-reset-line');
            const formLine = document.getElementById('form-line');
            const btnDrawPolygon = document.getElementById('btn-draw-polygon');
            const btnResetPolygon = document.getElementById('btn-reset-polygon');
            const formPolygon = document.getElementById('form-polygon');

            if (btnDrawLine) {
                btnDrawLine.className = "btn btn-outline-success w-100 btn-sm fw-bold py-2 mb-2";
                btnDrawLine.innerHTML = "🛣️ Mulai Gambar Jalur Truk";
            }
            if (btnResetLine) btnResetLine.classList.add('d-none');
            if (formLine) formLine.classList.add('d-none');

            if (btnDrawPolygon) {
                btnDrawPolygon.className = "btn btn-outline-success w-100 btn-sm fw-bold py-2 mb-2";
                btnDrawPolygon.innerHTML = "🟩 Mulai Gambar Batas Wilayah";
            }
            if (btnResetPolygon) btnResetPolygon.classList.add('d-none');
            if (formPolygon) formPolygon.classList.add('d-none');
        }

        // ==========================================
        // PROTEKSI GLOBAL EVENT LISTENER (PERBAIKAN)
        // ==========================================

        // 1. Event Listener Titik (Publik & Admin)
        document.getElementById('btn-draw-point')?.addEventListener('click', function() {
            isDrawing = !isDrawing;
            if (isDrawing) {
                currentActiveMode = 'point'; // <--- Set langsung tanpa resetState
                this.className = "btn btn-danger w-100 btn-sm fw-bold py-2 mb-3";
                this.innerHTML = "🛑 Batalkan Penempatan";
                mapContainer?.classList.add('drawing-mode');
            } else {
                resetDrawingState();
            }
        });

        document.getElementById('btn-geolocate')?.addEventListener('click', function() {
            const geoButton = this;
            if (!navigator.geolocation) {
                alert('Peramban Anda tidak mendukung geolokasi. Silakan pilih lokasi secara manual di peta.');
                return;
            }

            geoButton.disabled = true;
            geoButton.innerHTML = '⏳ Mencari Lokasi...';

            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude.toFixed(6);
                const lng = position.coords.longitude.toFixed(6);
                const latInp = document.getElementById('lat-point');
                const lngInp = document.getElementById('lng-point');
                const formPoint = document.getElementById('form-point');

                if (latInp) latInp.value = lat;
                if (lngInp) lngInp.value = lng;
                if (formPoint) formPoint.classList.remove('d-none');

                const newLatLng = L.latLng(position.coords.latitude, position.coords.longitude);
                if (tempMarker) {
                    tempMarker.setLatLng(newLatLng);
                } else {
                    tempMarker = L.marker(newLatLng).addTo(map);
                }
                map.setView(newLatLng, 17);
                if (!isDrawing) {
                    isDrawing = true;
                    document.getElementById('btn-draw-point').className = "btn btn-danger w-100 btn-sm fw-bold py-2 mb-3";
                    document.getElementById('btn-draw-point').innerHTML = "🛑 Batalkan Penempatan";
                    mapContainer?.classList.add('drawing-mode');
                }

                geoButton.innerHTML = '📍 Lokasi Saya';
                geoButton.disabled = false;
            }, function(error) {
                alert('Gagal mengambil lokasi. Pastikan izin lokasi diizinkan dan coba lagi.');
                geoButton.innerHTML = '📍 Lokasi Saya';
                geoButton.disabled = false;
            }, {
                enableHighAccuracy: true,
                timeout: 12000,
                maximumAge: 0
            });
        });

        // 2. Event Listener Garis/Rute (Hanya Admin)
        document.getElementById('btn-draw-line')?.addEventListener('click', function() {
            isDrawing = !isDrawing;
            if (isDrawing) {
                currentActiveMode = 'line';
                this.className = "btn btn-warning text-dark w-100 btn-sm fw-bold py-2 mb-2";
                this.innerHTML = "🏁 Selesai Gambar Rute";
                document.getElementById('btn-reset-line')?.classList.remove('d-none');
                document.getElementById('form-line')?.classList.remove('d-none');
                mapContainer?.classList.add('drawing-mode');
                tempPolyline = L.polyline([], {
                    color: '#0d6efd',
                    weight: 5
                }).addTo(map);
            } else {
                if (tempPointsArray.length < 2) {
                    alert('Silakan klik minimal 2 titik lokasi di peta untuk jalur rute!');
                    isDrawing = true;
                    return;
                }
                mapContainer?.classList.remove('drawing-mode');
                this.className = "btn btn-success w-100 btn-sm fw-bold py-2 mb-2";
                this.innerHTML = "✅ Jalur Rute Terkunci";

                let inputCoords = document.getElementById('coords-line');
                if (inputCoords) {
                    inputCoords.value = JSON.stringify(tempPointsArray);
                }
            }
        });

        // 3. Event Listener Area/Zona (Hanya Admin)
        document.getElementById('btn-draw-polygon')?.addEventListener('click', function() {
            isDrawing = !isDrawing;
            if (isDrawing) {
                currentActiveMode = 'polygon';
                this.className = "btn btn-warning text-dark w-100 btn-sm fw-bold py-2 mb-2";
                this.innerHTML = "🏁 Selesai Gambar Area";
                document.getElementById('btn-reset-polygon')?.classList.remove('d-none');
                document.getElementById('form-polygon')?.classList.remove('d-none');
                mapContainer?.classList.add('drawing-mode');
                tempPolygon = L.polygon([], {
                    color: '#10b981',
                    fillColor: '#10b981',
                    fillOpacity: 0.3
                }).addTo(map);
            } else {
                if (tempPointsArray.length < 3) {
                    alert('Silakan klik minimal 3 titik sudut lokasi di peta untuk membentuk area wilayah!');
                    isDrawing = true;
                    return;
                }
                mapContainer?.classList.remove('drawing-mode');
                this.className = "btn btn-success w-100 btn-sm fw-bold py-2 mb-2";
                this.innerHTML = "✅ Batas Area Terkunci";
            }
        });

        // LOGIKA EVENT KLIK DI ATAS PETA UTAMA
        map.on('click', function(e) {
            if (!isDrawing) return;

            let lat = e.latlng.lat;
            let lng = e.latlng.lng;

            if (currentActiveMode === 'point') {
                let latInp = document.getElementById('lat-point');
                let lngInp = document.getElementById('lng-point');
                if (latInp) latInp.value = lat.toFixed(6);
                if (lngInp) lngInp.value = lng.toFixed(6);
                document.getElementById('form-point')?.classList.remove('d-none');

                if (tempMarker) {
                    tempMarker.setLatLng(e.latlng);
                } else {
                    tempMarker = L.marker(e.latlng).addTo(map);
                }

            } else if (currentActiveMode === 'line' && tempPolyline) {
                tempPointsArray.push([lat, lng]);
                tempPolyline.setLatLngs(tempPointsArray);
                let input = document.getElementById('coords-line');
                if (input) input.value = JSON.stringify(tempPointsArray);

            } else if (currentActiveMode === 'polygon' && tempPolygon) {
                tempPointsArray.push([lat, lng]);
                tempPolygon.setLatLngs(tempPointsArray);
                let input = document.getElementById('coords-polygon');
                if (input) input.value = JSON.stringify(tempPointsArray);
            }
        });

        // ==========================================
        // FETCH DATA DAN RENDERING MAPS LEAFLET
        // ==========================================
        fetch('/api/points').then(r => r.json()).then(data => {
            L.geoJSON(data, {
                pointToLayer: function(f, latlng) {
                    let color = f.properties.status === 'Selesai' ? 'green' : (f.properties.status ===
                        'Proses' ? 'orange' : 'red');
                    return L.marker(latlng, {
                        icon: L.icon({
                            iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    });
                },
                onEachFeature: function(f, layer) {
                    let statusColor = f.properties.status === 'Selesai' ? '#10b981' : (f.properties.status === 'Proses' ? '#f59e0b' : '#ef4444');
                    let statusLabel = f.properties.status || 'Belum diproses';
                    let img = f.properties.image ?
                        `<img src="/uploads/${f.properties.image}" class="popup-image" alt="Foto Bukti">` :
                        '';
                    layer.bindPopup(
                        `<div class="popup-card">
                            <h6 class="popup-title">📍 ${f.properties.name}</h6>
                            <div class="popup-subtitle"><span class="badge" style="background:${statusColor};color:#ffffff;">${statusLabel}</span></div>
                            ${f.properties.description ? `<p class="popup-text">${f.properties.description}</p>` : ''}
                            ${img}
                            <div class="popup-meta">
                                <div><strong>Latitude:</strong> ${f.geometry.coordinates[1].toFixed(6)}</div>
                                <div><strong>Longitude:</strong> ${f.geometry.coordinates[0].toFixed(6)}</div>
                            </div>
                        </div>`
                    );
                }
            }).addTo(map);
        }).catch(e => {});

        fetch('/api/routes').then(r => r.json()).then(d => {
            L.geoJSON(d, {
                style: {
                    color: '#3b82f6',
                    weight: 5,
                    opacity: 0.85
                },
                onEachFeature: function(f, layer) {
                    layer.bindPopup(
                        `<div class="popup-card">
                            <h6 class="popup-title">Route: ${f.properties.name}</h6>
                            <div class="popup-subtitle"><span class="badge bg-primary text-white">Rute Pengangkutan</span></div>
                            <p class="popup-text">Jadwal pengangkutan terencana untuk rute operasional ini.</p>
                            <div class="popup-meta"><strong>Jadwal:</strong> ${f.properties.schedule || '-'}</div>
                        </div>`
                    );
                }
            }).addTo(map);
        }).catch(e => {});

        fetch('/api/zones').then(r => r.json()).then(d => {
            L.geoJSON(d, {
                style: {
                    color: '#10b981',
                    fillColor: '#10b981',
                    fillOpacity: 0.18,
                    weight: 3
                },
                onEachFeature: function(f, layer) {
                    layer.bindPopup(
                        `<div class="popup-card">
                            <h6 class="popup-title">Zone: ${f.properties.zone_name}</h6>
                            <div class="popup-subtitle"><span class="badge bg-success text-white">Zona Monitoring</span></div>
                            <p class="popup-text">Batas area operasional atau pemantauan kebersihan Krapyak.</p>
                            <div class="popup-meta"><strong>Luas Area:</strong> ${f.properties.area_size || '0'} m²</div>
                        </div>`
                    );
                }
            }).addTo(map);
        }).catch(e => {});
    </script>
@endsection
