<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientAppoinmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_appoinments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('doctor_service_id')->nullable();
            $table->enum('day', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->string('time_start', 5);
            $table->string('time_end', 5);
            $table->enum('status', ['Menunggu', 'Selesai', 'Dibatalkan']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
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
        Schema::dropIfExists('patient_appoinments');
    }
}
