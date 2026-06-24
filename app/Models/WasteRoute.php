<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteRoute extends Model
{
    // 1. Tentukan nama tabel di database secara eksplisit
    protected $table = 'waste_routes';

    // 2. Izinkan kolom-kolom ini agar bisa menyimpan data dari form
    protected $fillable = [
        'name',
        'schedule',
        'distance',
        'geom'
    ];
}
