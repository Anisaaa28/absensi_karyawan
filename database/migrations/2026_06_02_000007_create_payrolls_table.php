<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('worked_days')->default(0);
            $table->decimal('worked_hours', 10, 2)->default(0);
            $table->decimal('ot_hours', 10, 2)->default(0);
            $table->decimal('gross_pay', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->enum('status', ['Draft', 'Approved', 'Finalized'])->default('Draft');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'period_start', 'period_end']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
};
