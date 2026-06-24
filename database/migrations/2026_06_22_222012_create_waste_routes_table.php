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
        Schema::create('waste_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('distance')->nullable();
            $table->geometry('geom', 'linestring', 4326); // Perbaikan sintaks PostGIS LineString/Polyline
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_routes');
    }
};
