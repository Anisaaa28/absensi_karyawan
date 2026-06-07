<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Data lama tersimpan sebagai UTC karena config('app.timezone') = 'UTC'.
        // Geser +7 jam (WIB = UTC+7) agar konsisten dengan timezone baru.
        DB::statement("UPDATE attendances SET clock_in = clock_in + INTERVAL '7 hours' WHERE clock_in IS NOT NULL");
        DB::statement("UPDATE attendances SET clock_out = clock_out + INTERVAL '7 hours' WHERE clock_out IS NOT NULL");
    }

    public function down()
    {
        // Rollback: kembalikan -7 jam
        DB::statement("UPDATE attendances SET clock_in = clock_in - INTERVAL '7 hours' WHERE clock_in IS NOT NULL");
        DB::statement("UPDATE attendances SET clock_out = clock_out - INTERVAL '7 hours' WHERE clock_out IS NOT NULL");
    }
};
