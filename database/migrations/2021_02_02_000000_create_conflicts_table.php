<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConflictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_worktime_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quota');
            $table->string('time_start', 5);
            $table->string('time_end', 5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conflicts');
    }
}
