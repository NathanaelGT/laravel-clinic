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
            $table->unsignedBigInteger('doctor_service_id')->nullable();
            $table->unsignedInteger('quota');
            $table->string('day');
            $table->time('time_start');
            $table->time('time_end');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('doctor_service_id')->references('id')->on('doctor_services')->nullOnDelete();
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
