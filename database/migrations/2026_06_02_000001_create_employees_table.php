<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nik')->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->enum('type', ['Security', 'Cleaning Service', 'Helper'])->default('Helper');
            $table->string('photo_path')->nullable();
            $table->date('joined_at')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('address')->nullable();
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
