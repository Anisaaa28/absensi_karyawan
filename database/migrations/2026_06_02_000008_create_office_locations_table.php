<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('office_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('radius')->default(500);
            $table->timestamps();
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');
            DB::statement('ALTER TABLE office_locations ADD COLUMN IF NOT EXISTS geofence_center geography(Point, 4326)');
            DB::statement('CREATE INDEX IF NOT EXISTS office_locations_geofence_center_gist ON office_locations USING GIST (geofence_center)');
        }
    }

    public function down()
    {
        Schema::dropIfExists('office_locations');
    }
};
