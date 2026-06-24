<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    // =========================================================================
    // 1. API DATA SPASIAL JALUR PUBLIK (UNTUK DIREAD OLEH LEAFLET MAP)
    // =========================================================================

    /**
     * Mengambil data titik sampah dalam format GeoJSON standar (Tahan Error 500)
     */
    public function index()
    {
        try {
            $points = DB::select("
                SELECT id, name, description, status, image,
                    ST_Y(geom::geometry) as latitude,
                    ST_X(geom::geometry) as longitude
                FROM waste_points
            ");

            $features = [];
            foreach ($points as $point) {
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            is_numeric($point->longitude) ? (float)$point->longitude : 0.0,
                            is_numeric($point->latitude) ? (float)$point->latitude : 0.0
                        ]
                    ],
                    'properties' => [
                        'id' => $point->id,
                        'name' => $point->name,
                        'description' => $point->description,
                        'status' => $point->status,
                        'image' => $point->image,
                    ]
                ];
            }

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $features
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil data garis rute pengangkutan sampah (LineString GeoJSON)
     */
    public function getRoutes()
    {
        // PERBAIKAN: Menarik kolom 'schedule' dari database agar muncul di pop-up peta
        $routes = DB::select("SELECT id, name, schedule, distance, ST_AsGeoJSON(geom) as geojson FROM waste_routes");

        $features = [];
        foreach ($routes as $route) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($route->geojson),
                'properties' => [
                    'id' => $route->id,
                    'name' => $route->name,
                    'schedule' => $route->schedule, // Dioper ke properti GeoJSON Leaflet
                    'distance' => $route->distance,
                ]
            ];
        }
        return response()->json(['type' => 'FeatureCollection', 'features' => $features]);
    }

    /**
     * Mengambil data area zonasi kebersihan wilayah (Polygon GeoJSON)
     */
    public function getZones()
    {
        // PERBAIKAN: Menyesuaikan pemanggilan kolom luas dari 'area' menjadi 'area_size' sesuai database PostgreSQL kamu
        $zones = DB::select("SELECT id, name, area_size, ST_AsGeoJSON(geom) as geojson FROM waste_zones");

        $features = [];
        foreach ($zones as $zone) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($zone->geojson),
                'properties' => [
                    'id' => $zone->id,
                    'zone_name' => $zone->name, // Disesuaikan dengan pemanggilan js Leaflet: f.properties.zone_name
                    'area_size' => $zone->area_size, // Dioper ke properti GeoJSON Leaflet
                ]
            ];
        }
        return response()->json(['type' => 'FeatureCollection', 'features' => $features]);
    }


    // =========================================================================
    // 2. AKSI INPUT PENGGUNA (DARI FORMSIDEBAR MAP)
    // =========================================================================

    /**
     * Menyimpan data laporan titik sampah baru dari masyarakat publik
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads'), $imageName);
        }

        DB::insert(
            "INSERT INTO waste_points (name, description, status, image, geom, created_at, updated_at)
            VALUES (?, ?, 'Belum Ditangani', ?, ST_GeomFromText(?, 4326), NOW(), NOW())",
            [
                $request->name,
                $request->description,
                $imageName,
                "POINT(" . $request->longitude . " " . $request->latitude . ")"
            ]
        );

        return redirect()->route('map')->with('success', 'Laporan geospasial tumpukan sampah berhasil dikirim!');
    }

    // Catatan: Fungsi storeRoute dan storeZone sengaja dikosongkan karena alur penyimpanan baru
    // saat ini sudah dialihkan secara rapi ke RouteController & ZoneController via routes/web.php
    public function storeRoute(Request $request) {}
    public function storeZone(Request $request) {}
}
