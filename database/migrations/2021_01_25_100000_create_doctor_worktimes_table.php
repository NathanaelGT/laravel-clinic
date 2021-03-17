<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorWorktimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor_worktimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_service_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quota');
            $table->enum('day', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->time('time_start');
            $table->time('time_end');
            $table->foreignId('replaced_with_id')->nullable()->constrained('doctor_worktimes')->nullOnDelete();
            $table->date('active_date')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctor_worktimes');
    }
}
