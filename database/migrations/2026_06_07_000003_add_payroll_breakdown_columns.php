<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('allowance', 12, 2)->default(0)->after('base_salary');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('ot_pay', 12, 2)->default(0)->after('ot_hours');
            $table->decimal('allowance', 12, 2)->default(0)->after('ot_pay');
            $table->decimal('late_penalty', 12, 2)->default(0)->after('deductions');
            $table->decimal('tax', 12, 2)->default(0)->after('late_penalty');
            $table->unsignedInteger('present_days')->default(0)->after('tax');
            $table->unsignedInteger('late_days')->default(0)->after('present_days');
            $table->unsignedInteger('absent_days')->default(0)->after('late_days');
            $table->text('notes')->nullable()->after('absent_days');
            $table->foreignId('approved_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->foreignId('finalized_by')->nullable()->after('approved_by')->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['finalized_by']);
            $table->dropColumn([
                'ot_pay', 'allowance', 'late_penalty', 'tax',
                'present_days', 'late_days', 'absent_days', 'notes',
                'approved_by', 'finalized_by',
            ]);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('allowance');
        });
    }
};
