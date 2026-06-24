<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteZone extends Model
{
    // 1. Tentukan nama tabel di database secara eksplisit
    protected $table = 'waste_zones';

    // 2. Izinkan kolom-kolom ini agar bisa menyimpan data dari form
    protected $fillable = [
        'name', // Pastikan di database bernama 'name' atau 'zone_name' sesuai migrasimu
        'area_size',
        'geom'
    ];
}
