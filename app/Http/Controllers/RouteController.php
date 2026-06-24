<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    public function edit($id)
    {
        // Ambil data rute lama dan konversi koordinatnya ke GeoJSON agar terbaca oleh JavaScript Leaflet
        $route = DB::select("
        SELECT id, name, schedule, ST_AsGeoJSON(geom) as coordinates
        FROM waste_routes
        WHERE id = ?
    ", [$id]);

        // Pastikan data rute ditemukan
        if (empty($route)) {
            return redirect()->route('dashboard')->with('error', 'Data rute tidak ditemukan.');
        }

        // Ambil index pertama karena hasil DB::select berupa array
        $route = $route[0];

        return view('routes-edit', compact('route'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi input termasuk schedule
        $request->validate([
            'name' => 'required|string|max:255',
            'schedule' => 'required|string|max:255',
            'coordinates' => 'required|string'
        ]);

        // 2. Cek apakah koordinatnya berupa array mentah atau GeoJSON
        $coordsArray = json_decode($request->coordinates, true);

        // Jika formatnya dibungkus GeoJSON objek dari Leaflet, ambil array koordinatnya saja
        if (isset($coordsArray['coordinates'])) {
            $coordsArray = $coordsArray['coordinates'];
        }

        // 3. Konversi array [lat, lng] menjadi format WKT LINESTRING (Lng Lat) untuk PostGIS
        $linestringCoords = [];
        foreach ($coordsArray as $coord) {
            // Handle jika formatnya [lat, lng] atau [lng, lat]
            if (isset($coord['lat']) && isset($coord['lng'])) {
                $linestringCoords[] = $coord['lng'] . ' ' . $coord['lat'];
            } else {
                // Standar balik Leaflet [lat, lng] -> PostGIS [lng, lat]
                $linestringCoords[] = $coord[1] . ' ' . $coord[0];
            }
        }
        $linestringWkt = 'LINESTRING(' . implode(', ', $linestringCoords) . ')';

        // 4. Eksekusi Query UPDATE ke Database PostGIS (Suntikkan $request->schedule di sini!)
        DB::update("
        UPDATE waste_routes
        SET
            name = ?,
            schedule = ?,
            distance = ST_Length(ST_GeomFromText(?, 4326)::geography),
            geom = ST_GeomFromText(?, 4326),
            updated_at = NOW()
        WHERE id = ?
    ", [
            $request->name,
            $request->schedule, // <--- Ini dia penyelamat kita!
            $linestringWkt,
            $linestringWkt,
            $id
        ]);

        return redirect()->route('dashboard')->with('success', 'Jalur rute dan jadwal berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        DB::delete("DELETE FROM waste_routes WHERE id = ?", [$id]);
        return redirect()->route('dashboard')->with('success', 'Data rute berhasil dihapus!');
    }

    public function store(Request $request)
    {
        // 1. Validasi pastikan semua input terbaca
        $request->validate([
            'name' => 'required|string|max:255',
            'schedule' => 'required|string|max:255',
            'coordinates' => 'required|string'
        ]);

        $coordsArray = json_decode($request->coordinates, true);

        if (empty($coordsArray) || count($coordsArray) < 2) {
            return redirect()->back()->with('error', 'Gagal menyimpan, koordinat rute tidak valid.');
        }

        // 2. Susun format LINESTRING WKT (Longitude Latitude)
        $linestringCoords = [];
        foreach ($coordsArray as $coord) {
            $linestringCoords[] = $coord[1] . ' ' . $coord[0];
        }
        $linestringWkt = 'LINESTRING(' . implode(', ', $linestringCoords) . ')';

        // 3. Simpan ke database dengan kalkulasi meter yang presisi (menggunakan cast ::geography)
        DB::insert("
        INSERT INTO waste_routes (name, schedule, distance, geom, created_at, updated_at)
        VALUES (
            ?,
            ?,
            ST_Length(ST_GeomFromText(?, 4326)::geography),
            ST_GeomFromText(?, 4326),
            NOW(),
            NOW()
        )
    ", [
            $request->name,
            $request->schedule, // Ini akan masuk ke kolom schedule databasemu
            $linestringWkt,     // Masuk ke kalkulasi panjang meter
            $linestringWkt      // Masuk ke data geometri peta
        ]);

        return redirect()->route('dashboard')->with('success', 'Rute baru dan jarak meter berhasil disimpan!');
    }
}
