@extends('template')

@section('title', 'Perbarui Area Zona - wasteCare')

@section('styles')
    <style>
        body {
            background-color: #f8fafc;
        }

        .edit-container {
            display: flex;
            height: calc(100vh - 66px);
            min-height: calc(100vh - 66px);
            position: relative;
            background-color: #eef2f7;
        }

        /* Sleek Sidebar styling */
        .form-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            bottom: 20px;
            width: 360px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12), 0 1px 3px rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            z-index: 1000;
            padding: 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        #edit-map {
            flex-grow: 1;
            height: 100%;
            width: 100%;
            z-index: 1;
        }

        .drawing-active {
            cursor: crosshair !important;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
            margin-bottom: 1.25rem;
        }

        .btn-back:hover {
            color: #0f172a;
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
    </style>
@endsection

@section('content')
    <div class="edit-container">
        <div class="form-panel">
            <a href="/dashboard" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
            <h5 class="fw-bold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.02em;">Edit Area Zona</h5>
            <span
                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small align-self-start mb-3"
                style="background: rgba(16, 185, 129, 0.08); color: #10b981; border-color: rgba(16, 185, 129, 0.15); font-size: 0.72rem; font-weight: 600;">{{ $zone->zone_name }}</span>
            <p class="info-tag">Klik tombol <b>Plot Area Baru</b> lalu tentukan sudut-sudut wilayah di peta untuk membentuk
                area poligon cakupan baru yang melingkari daerah monitoring.</p>

            <form action="{{ route('zones.update', $zone->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="section-label">Nama Zona / Wilayah</label>
                    <input type="text" name="zone_name" class="form-control form-control-sm"
                        value="{{ $zone->zone_name }}" required placeholder="Contoh: RT 01 / Komplek B">
                </div>

                <div class="mb-3">
                    <label class="section-label d-block">Luas Area Saat Ini</label>
                    <span class="fw-bold text-success" style="font-size: 1.1rem;">{{ number_format($zone->area_size, 2) }}
                        m²</span>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">*Luas akan dikalkulasi ulang otomatis oleh
                        sistem jika Anda menggambar ulang area baru.</small>
                </div>

                <div class="mb-3">
                    <button type="button" id="btn-re-draw"
                        class="btn btn-outline-success btn-sm w-100 fw-bold py-2 mb-2">🔄 Plot Area Baru</button>
                    <button type="button" id="btn-pop-node"
                        class="btn btn-link text-danger text-decoration-none w-100 p-0 d-none"
                        style="font-size: 0.8rem; font-weight: 600;">Hapus Sudut Terakhir</button>
                </div>

                <div class="mb-3">
                    <label class="section-label">Koordinat Area (JSON)</label>
                    <textarea id="coords-output" name="coordinates" class="form-control form-control-sm bg-light" rows="4" readonly
                        required style="font-family: monospace; font-size: 0.75rem;">{{ $zone->coordinates }}</textarea>
                </div>

                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold py-2 shadow-sm"><i
                        class="fa-regular fa-floppy-disk me-1.5"></i> Simpan Perubahan</button>
            </form>
        </div>

        <div id="edit-map"></div>
    </div>
@endsection

@section('scripts')
    <script>
        // 1. Inisialisasi Peta
        var map = L.map('edit-map').setView([-7.8256, 110.3630], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        let rawCoords = document.getElementById('coords-output').value;
        let tempPointsArray = [];
        let currentPolygon = null;
        let isDrawing = false;

        // 2. Membaca dan memplot poligon area lama dari PostGIS
        if (rawCoords) {
            try {
                let geoJsonData = JSON.parse(rawCoords);

                // Toleransi deteksi tipe koordinat balik
                if (geoJsonData.coordinates && geoJsonData.coordinates[0]) {
                    tempPointsArray = geoJsonData.coordinates[0].map(coord => [coord[1], coord[0]]);
                    tempPointsArray.pop(); // Buang titik duplikat penutup ring dari PostGIS
                } else if (Array.isArray(geoJsonData)) {
                    tempPointsArray = geoJsonData;
                }

                // Gambar poligon lama ke peta
                currentPolygon = L.polygon(tempPointsArray, {
                    color: '#10b981',
                    fillColor: '#10b981',
                    fillOpacity: 0.18,
                    weight: 3
                }).addTo(map);
                map.fitBounds(currentPolygon.getBounds());

                // Set default value textarea berupa array bersih
                document.getElementById('coords-output').value = JSON.stringify(tempPointsArray);
            } catch (e) {
                console.error("Gagal memproses koordinat area", e);
            }
        }

        // 3. Logika Menggambar Poligon Baru
        const btnReDraw = document.getElementById('btn-re-draw');
        const btnPopNode = document.getElementById('btn-pop-node');
        const mapDiv = document.getElementById('edit-map');

        btnReDraw.addEventListener('click', function() {
            isDrawing = !isDrawing;
            if (isDrawing) {
                this.className = "btn btn-warning text-dark btn-sm w-100 fw-bold py-2 mb-2";
                this.innerHTML = "🏁 Kunci Area Baru";
                btnPopNode.classList.remove('d-none');
                mapDiv.classList.add('drawing-active');

                if (currentPolygon) {
                    map.removeLayer(currentPolygon);
                }
                tempPointsArray = [];
                currentPolygon = L.polygon([], {
                    color: '#20c997',
                    fillColor: '#20c997',
                    fillOpacity: 0.4,
                    weight: 3
                }).addTo(map);
                document.getElementById('coords-output').value = "";
            } else {
                if (tempPointsArray.length < 3) {
                    alert('Area wilayah minimal membutuhkan 3 sudut klik!');
                    isDrawing = true;
                    return;
                }
                this.className = "btn btn-outline-success btn-sm w-100 fw-bold py-2 mb-2";
                this.innerHTML = "🔄 Plot Area Baru";
                btnPopNode.classList.add('d-none');
                mapDiv.classList.remove('drawing-active');
            }
        });

        // Event Klik Peta saat menggambar poligon
        map.on('click', function(e) {
            if (!isDrawing) return;

            tempPointsArray.push([e.latlng.lat, e.latlng.lng]);
            currentPolygon.setLatLngs(tempPointsArray);
            document.getElementById('coords-output').value = JSON.stringify(tempPointsArray);
        });

        // Undo sudut terakhir
        btnPopNode.addEventListener('click', function() {
            if (tempPointsArray.length > 0 && currentPolygon) {
                tempPointsArray.pop();
                currentPolygon.setLatLngs(tempPointsArray);
                document.getElementById('coords-output').value = tempPointsArray.length > 0 ? JSON.stringify(
                    tempPointsArray) : "";
            }
        });
    </script>
@endsection
