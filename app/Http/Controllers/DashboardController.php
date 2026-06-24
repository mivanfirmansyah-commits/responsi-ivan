<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib gunakan Facade DB

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil data titik sampah, rute (garis), dan zona (area) menggunakan Raw Query PostGIS
        $points = DB::select("SELECT id, name, description, status, image, created_at FROM waste_points ORDER BY created_at DESC");

        $routes = DB::select("
        SELECT id, name, schedule, distance, created_at
        FROM waste_routes
        ORDER BY created_at DESC
    ");

        $zones = DB::select("SELECT id, name as zone_name, area_size, ST_AsGeoJSON(geom) as geojson FROM waste_zones ORDER BY created_at DESC");

        // Lempar ke view dashboard
        return view('dashboard', compact('points', 'routes', 'zones'));
    }

    /**
     * Memperbarui status penanganan titik sampah dari dashboard admin
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Belum Ditangani,Proses,Selesai'
        ]);

        DB::update("UPDATE waste_points SET status = ?, updated_at = NOW() WHERE id = ?", [
            $request->status,
            $id
        ]);

        return redirect()->route('dashboard')->with('success', 'Status laporan berhasil diperbarui!');
    }

    /**
     * Menghapus laporan sampah secara permanen beserta file gambar fisiknya
     */
    public function destroyPoint($id)
    {
        $point = DB::selectOne("SELECT image FROM waste_points WHERE id = ?", [$id]);

        if ($point && $point->image) {
            $imagePath = public_path('uploads/' . $point->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        DB::delete("DELETE FROM waste_points WHERE id = ?", [$id]);

        return redirect()->route('dashboard')->with('success', 'Laporan sampah telah berhasil dihapus!');
    }
}
