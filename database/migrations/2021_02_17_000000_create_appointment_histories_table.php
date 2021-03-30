<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_histories', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('doctor');
            $table->string('service');
            $table->foreignId('doctor_service_id')->nullable()->constrained()->nullOnDelete();
            $table->time('time_start');
            $table->time('time_end');
            $table->foreignId('doctor_worktime_id')->nullable()->constrained()->nullOnDelete();
            $table->string('patient_name');
            $table->string('patient_nik', 15);
            $table->string('patient_phone_number', 15);
            $table->string('patient_address');
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['Menunggu', 'Selesai', 'Dibatalkan', 'Reschedule', 'Konflik']);
            $table->foreignId('reschedule_id')->nullable()->constrained('appointment_histories')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_histories');
    }
}
