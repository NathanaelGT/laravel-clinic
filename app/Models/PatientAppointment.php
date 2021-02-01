<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PatientAppointment
 *
 * @property int $id
 * @property int|null $patient_id
 * @property int|null $service_appointment_id
 * @property string $status
 * @property string|null $deleted_at
 * @property-read \App\Models\Patient|null $patient
 * @property-read \App\Models\ServiceAppointment|null $serviceAppointment
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppointment query()
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppointment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppointment wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppointment whereServiceAppointmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppointment whereStatus($value)
 * @mixin \Eloquent
 */
class PatientAppointment extends Model
{
    use HasFactory;

    public $fillable = ['patient_id', 'service_appointment_id', 'status'];
    public $timestamps = false;

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function serviceAppointment()
    {
        return $this->belongsTo(ServiceAppointment::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->with('patient', 'serviceAppointment.doctorWorktime.doctorService.service')
            ->whereId($value)->whereStatus('Menunggu')->firstOrFail();
    }
}
