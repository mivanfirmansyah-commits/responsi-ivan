<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZoneController extends Controller
{
    public function edit($id)
    {
        // PERBAIKAN: Mengambil kolom 'area_size' dari database, di-alias sebagai area_size
        $zone = DB::selectOne("SELECT id, name as zone_name, ST_AsGeoJSON(geom) as coordinates, area_size FROM waste_zones WHERE id = ?", [$id]);

        if (!$zone) {
            return redirect('/dashboard')->with('error', 'Data zona tidak ditemukan.');
        }

        return view('zones-edit', compact('zone'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'zone_name' => 'required|string|max:255',
            'coordinates' => 'required|string'
        ]);

        $rawCoords = json_decode($request->coordinates, true);

        // Antisipasi jika koordinat dibungkus format objek GeoJSON oleh Leaflet
        if (isset($rawCoords['coordinates'][0])) {
            $rawCoords = $rawCoords['coordinates'][0];
        }

        $wktPairs = [];
        foreach ($rawCoords as $coord) {
            // Cek format array Leaflet [lat, lng] atau objek {lat, lng}
            if (isset($coord['lat']) && isset($coord['lng'])) {
                $wktPairs[] = $coord['lng'] . ' ' . $coord['lat'];
            } else {
                $wktPairs[] = $coord[1] . ' ' . $coord[0]; // Lng Lat
            }
        }

        // Syarat Poligon PostGIS: Titik akhir harus sama dengan titik awal untuk menutup ring
        if (isset($rawCoords[0]['lat']) && isset($rawCoords[0]['lng'])) {
            $wktPairs[] = $rawCoords[0]['lng'] . ' ' . $rawCoords[0]['lat'];
        } else {
            $wktPairs[] = $rawCoords[0][1] . ' ' . $rawCoords[0][0];
        }

        $wktPolygon = 'POLYGON((' . implode(',', $wktPairs) . '))';

        // PERBAIKAN: Nama kolom disesuaikan menjadi area_size, dan luasnya dihitung otomatis menggunakan ST_Area PostGIS asli
        DB::update("
            UPDATE waste_zones
            SET
                name = ?,
                area_size = ST_Area(ST_GeomFromText(?, 4326)::geography),
                geom = ST_GeomFromText(?, 4326),
                updated_at = NOW()
            WHERE id = ?
        ", [
            $request->zone_name,
            $wktPolygon, // Digunakan oleh ST_Area
            $wktPolygon, // Digunakan oleh geom
            $id
        ]);

        return redirect('/dashboard')->with('success', 'Area cakupan wilayah berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        DB::delete("DELETE FROM waste_zones WHERE id = ?", [$id]);
        return redirect()->route('dashboard')->with('success', 'Data cakupan wilayah berhasil dihapus!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone_name' => 'required|string|max:255',
            'coordinates' => 'required|string'
        ]);

        $coordsArray = json_decode($request->coordinates, true);
        $polygonCoords = [];
        foreach ($coordsArray as $coord) {
            $polygonCoords[] = $coord[1] . ' ' . $coord[0]; // Balik ke Lng Lat
        }
        // PostGIS Polygon wajib menutup ring
        $polygonCoords[] = $coordsArray[0][1] . ' ' . $coordsArray[0][0];
        $polygonWkt = 'POLYGON((' . implode(', ', $polygonCoords) . '))';

        // PERBAIKAN: Menghitung area_size otomatis menggunakan ST_Area saat pertama kali ditambahkan
        DB::insert("
            INSERT INTO waste_zones (name, area_size, geom, created_at, updated_at)
            VALUES (?, ST_Area(ST_GeomFromText(?, 4326)::geography), ST_GeomFromText(?, 4326), NOW(), NOW())
        ", [
            $request->zone_name,
            $polygonWkt, // Untuk ST_Area
            $polygonWkt  // Untuk geom
        ]);

        return redirect()->route('dashboard')->with('success', 'Cakupan area wilayah baru berhasil disimpan!');
    }
}
