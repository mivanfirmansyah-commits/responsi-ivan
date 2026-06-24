<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waste_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Zona Hijau Masjid Krapyak, Blok RT 01
            $table->double('area')->nullable(); // Luas area (dalam meter persegi)
            $table->geometry('geom', 'polygon', 4326); // Perbaikan sintaks PostGIS Polygon di Laravel terbaru
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_zones');
    }
};
