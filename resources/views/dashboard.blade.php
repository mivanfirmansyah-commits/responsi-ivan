@extends('template')

@section('title', 'Dashboard Admin - wasteCare')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
        }

        .dashboard-container {
            padding: 3rem 1.5rem;
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Stat Cards styling */
        .stat-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 35px -15px rgba(16, 185, 129, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            font-weight: 700;
        }

        /* Dashboard card container */
        .table-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.06);
            border: 1px solid rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        /* Tab Navigation Styling */
        .nav-tabs-dashboard {
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            gap: 1.5rem;
        }

        .nav-tabs-dashboard .nav-link {
            color: #64748b;
            font-weight: 600;
            border: none;
            padding: 1rem 0.5rem;
            background: transparent;
            position: relative;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .nav-tabs-dashboard .nav-link:hover {
            color: #0f172a;
        }

        .nav-tabs-dashboard .nav-link.active {
            color: #10b981 !important;
            background: transparent;
            font-weight: 700;
        }

        .nav-tabs-dashboard .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #10b981;
            border-radius: 99px;
        }

        /* Table header & body styling overrides */
        .table {
            --bs-table-bg: transparent;
            --bs-table-hover-bg: rgba(16, 185, 129, 0.02);
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 0.82rem !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.25rem !important;
            border-bottom: 2px solid rgba(15, 23, 42, 0.06) !important;
            border-top: none !important;
        }

        .table tbody td {
            padding: 1.1rem 1.25rem !important;
            border-bottom: 1px solid rgba(15, 23, 42, 0.05) !important;
            font-size: 0.9rem;
            color: #334155;
        }

        /* Custom statuses design */
        .badge-status-selesai {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 99px;
            display: inline-block;
        }

        .badge-status-proses {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 99px;
            display: inline-block;
        }

        .badge-status-belum {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 99px;
            display: inline-block;
        }

        .status-select {
            min-width: 145px !important;
            font-size: 0.82rem !important;
            border-radius: 8px !important;
            border: 1px solid rgba(15, 23, 42, 0.12) !important;
            padding: 0.4rem 2rem 0.4rem 0.75rem !important;
        }

        .status-select:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12) !important;
        }

        /* DataTable components styling */
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 8px;
            padding: 0.25rem 1.5rem 0.25rem 0.5rem;
            outline: none;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            outline: none;
            margin-left: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .page-item.active .page-link {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
            color: white !important;
        }

        .page-link {
            color: #10b981;
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .page-link:hover {
            background-color: rgba(16, 185, 129, 0.05);
            color: #059669;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-container">

        <!-- Upper Banner -->
        <div class="row align-items-center mb-5 gy-4">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold" style="background: rgba(16, 185, 129, 0.08); color: #10b981; border-color: rgba(16, 185, 129, 0.15); font-size: 0.78rem;">🔐 Admin Panel</span>
                </div>
                <h2 class="fw-extrabold text-dark m-0" style="font-weight: 800; letter-spacing: -0.03em; font-size: 2.1rem;">Pusat Kontrol Spasial</h2>
                <p class="text-muted m-0 mt-2" style="font-size: 0.95rem; line-height: 1.5;">Kelola laporan masyarakat, pantau rute armada truk sampah, dan atur cakupan batas wilayah kebersihan secara terpusat.</p>
            </div>
            <div class="col-md-5 text-md-end">
                <a href="{{ route('map') }}" class="btn btn-success shadow-sm px-4 py-2.5"><i class="fa-solid fa-map-location-dot me-2"></i> Buka Peta Input</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-5 p-3.5 d-flex align-items-center gap-2" role="alert" style="border-radius: 14px; background: #ecfdf5; border-left: 4px solid #10b981 !important; color: #065f46;">
                <span style="font-size: 1.1rem;">⚡</span>
                <div class="flex-grow-1"><b>Berhasil!</b> {{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
        @endif

        <!-- Stats Overview Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Laporan Titik Sampah</div>
                            <h3 class="fw-extrabold text-dark mt-2 mb-0" style="font-weight: 800; font-size: 2rem;">{{ count($points) }}</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">📍</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Jalur Rute Truk</div>
                            <h3 class="fw-extrabold text-dark mt-2 mb-0" style="font-weight: 800; font-size: 2rem;">{{ count($routes) }}</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.08); color: #3b82f6;">🛣️</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Cakupan Zona Wilayah</div>
                            <h3 class="fw-extrabold text-dark mt-2 mb-0" style="font-weight: 800; font-size: 2rem;">{{ count($zones) }}</h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.08); color: #8b5cf6;">🟩</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card table-card border-0">
            <div class="card-header bg-white pt-4 px-4 pb-0 border-0">
                <ul class="nav nav-tabs nav-tabs-dashboard card-header-tabs" id="dashboardTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="laporan-tab" data-bs-toggle="tab"
                            data-bs-target="#panel-laporan" type="button" role="tab">
                            📍 Laporan Titik Sampah
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="rute-tab" data-bs-toggle="tab" data-bs-target="#panel-rute"
                            type="button" role="tab">
                            🛣️ Rute Truk (Garis)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="zona-tab" data-bs-toggle="tab" data-bs-target="#panel-zona"
                            type="button" role="tab">
                            🟩 Cakupan Wilayah (Area)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body tab-content p-4" id="dashboardTabContent">

                <!-- 1. TAB LAPORAN TITIK SAMPAH -->
                <div class="tab-pane fade show active" id="panel-laporan" role="tabpanel" aria-labelledby="laporan-tab">
                    <div class="table-responsive">
                        <table id="tableLaporan" class="table table-hover align-middle w-100">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 20%">Pelapor / Lokasi</th>
                                    <th style="width: 25%">Deskripsi Laporan</th>
                                    <th style="width: 15%">Foto Bukti</th>
                                    <th style="width: 15%">Status Saat Ini</th>
                                    <th style="width: 20%">Aksi Kelola</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($points as $index => $point)
                                    <tr>
                                        <td class="fw-bold text-secondary">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold d-block text-dark">{{ $point->name }}</span>
                                            <small class="text-muted d-block mt-0.5" style="font-size: 11px;">
                                                @if (isset($point->geom))
                                                    {{ is_object($point->geom) ? 'Geom Data' : 'Tersimpan (Spasial)' }}
                                                @else
                                                    📍 Koordinat Terpetakan
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <p class="mb-0 text-secondary small" style="line-height: 1.4;">
                                                {{ $point->description }}</p>
                                        </td>
                                        <td>
                                            @if ($point->image)
                                                <a href="/uploads/{{ $point->image }}" target="_blank">
                                                    <img src="/uploads/{{ $point->image }}"
                                                        class="img-fluid rounded border shadow-sm"
                                                        style="max-height: 50px; width: 70px; object-fit: cover; border-radius: 8px !important;"
                                                        alt="Bukti Sampah">
                                                </a>
                                            @else
                                                <span class="badge bg-light text-secondary border px-2.5 py-1 small rounded-pill">Tanpa Foto</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($point->status === 'Selesai')
                                                <span class="badge-status-selesai">Selesai</span>
                                            @elseif($point->status === 'Proses')
                                                <span class="badge-status-proses">Proses</span>
                                            @else
                                                <span class="badge-status-belum">Belum Ditangani</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('points.update', $point->id) }}" method="POST"
                                                    class="d-flex gap-1.5 m-0 flex-grow-1">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status"
                                                        class="form-select form-select-sm border-secondary-subtle status-select">
                                                        <option value="Belum Ditangani"
                                                            {{ $point->status == 'Belum Ditangani' ? 'selected' : '' }}>
                                                            Belum Ditangani</option>
                                                        <option value="Proses"
                                                            {{ $point->status == 'Proses' ? 'selected' : '' }}>Proses
                                                        </option>
                                                        <option value="Selesai"
                                                            {{ $point->status == 'Selesai' ? 'selected' : '' }}>Selesai
                                                        </option>
                                                    </select>
                                                    <button type="submit" class="btn btn-success btn-sm px-2.5"
                                                        title="Simpan Perubahan Status" style="border-radius: 8px !important;">
                                                        <i class="fa-regular fa-floppy-disk"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('points.destroy', $point->id) }}" method="POST"
                                                    class="m-0"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data laporan spasial ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-2.5"
                                                        title="Hapus Laporan" style="border-radius: 8px !important;">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Belum ada rekaman data laporan masuk dari masyarakat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. TAB JALUR RUTE TRUK -->
                <div class="tab-pane fade" id="panel-rute" role="tabpanel" aria-labelledby="rute-tab">
                    <div class="table-responsive">
                        <table id="tableRute" class="table table-hover align-middle w-100">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 25%">Nama Rute Operasional</th>
                                    <th style="width: 35%">Jadwal Pengangkutan / Catatan</th>
                                    <th style="width: 15%">Jarak Rute</th>
                                    <th style="width: 20%" class="text-center">Aksi Kelola</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routes as $index => $route)
                                    <tr>
                                        <td class="fw-bold text-secondary">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $route->name }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary">{{ $route->schedule ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2.5 py-1 fw-bold rounded-pill" style="font-size: 0.75rem;">{{ number_format($route->distance, 1) }} m</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1.5">
                                                <a href="{{ route('routes.edit', $route->id) }}"
                                                    class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1.5 fw-bold px-3 py-1.5 shadow-none" style="border-radius: 8px !important; font-size: 0.8rem;">
                                                    <i class="fa-regular fa-pen-to-square"></i> Edit Map
                                                </a>

                                                <form action="{{ route('routes.destroy', $route->id) }}" method="POST" class="m-0"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus rute ini secara permanen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger px-2.5 py-1.5" style="border-radius: 8px !important; font-size: 0.8rem;">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data rute pengangkutan sampah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. TAB CAKUPAN WILAYAH -->
                <div class="tab-pane fade" id="panel-zona" role="tabpanel" aria-labelledby="zona-tab">
                    <div class="table-responsive">
                        <table id="tableZona" class="table table-hover align-middle w-100">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 30%">Nama Wilayah / Cakupan Zona</th>
                                    <th style="width: 30%">Luas Area Estimasi</th>
                                    <th style="width: 15%">Jenis Geometris</th>
                                    <th style="width: 20%" class="text-center">Aksi Kelola</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($zones as $index => $zone)
                                    <tr>
                                        <td class="fw-bold text-secondary">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $zone->zone_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-success border px-2.5 py-1 fw-bold rounded-pill" style="font-size: 0.75rem;">{{ $zone->area_size ?? 0 }} m²</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded" style="background: rgba(16, 185, 129, 0.08); color: #10b981; font-size: 0.75rem; font-weight: 600;">🟩 Polygon</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1.5">
                                                <a href="{{ route('zones.edit', $zone->id) }}"
                                                    class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1.5 fw-bold px-3 py-1.5 shadow-none" style="border-radius: 8px !important; font-size: 0.8rem;">
                                                    <i class="fa-regular fa-pen-to-square"></i> Edit Map
                                                </a>

                                                <form action="{{ route('zones.destroy', $zone->id) }}" method="POST" class="m-0"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus zona area ini secara permanen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger px-2.5 py-1.5" style="border-radius: 8px !important; font-size: 0.8rem;">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data cakupan zona wilayah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables untuk Tabel Laporan Sampah
            $('#tableLaporan').DataTable({
                "language": {
                    "search": "Cari Laporan:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Data laporan tidak ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": {
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });

            // Inisialisasi DataTables untuk Tabel Rute Baru (Ditambahkan ID pada HTML)
            $('#tableRute').DataTable({
                "language": {
                    "search": "Cari Rute:",
                    "lengthMenu": "Tampilkan _MENU_ rute",
                    "zeroRecords": "Data rute tidak ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ rute",
                    "paginate": {
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });

            // Inisialisasi DataTables untuk Tabel Zona Baru (Ditambahkan ID pada HTML)
            $('#tableZona').DataTable({
                "language": {
                    "search": "Cari Zona:",
                    "lengthMenu": "Tampilkan _MENU_ zona",
                    "zeroRecords": "Data zona tidak ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ zona",
                    "paginate": {
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        });
    </script>
@endsection
