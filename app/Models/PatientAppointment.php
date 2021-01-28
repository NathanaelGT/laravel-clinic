<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PatientAppoinment
 *
 * @property int $id
 * @property int|null $patient_id
 * @property int|null $service_appointment_id
 * @property string $day
 * @property string $time_start
 * @property string $time_end
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Patient|null $patient
 * @property-read \App\Models\ServiceAppointment|null $serviceAppointment
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment newQuery()
 * @method static \Illuminate\Database\Query\Builder|PatientAppoinment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment query()
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereServiceAppointmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PatientAppoinment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PatientAppoinment withoutTrashed()
 * @mixin \Eloquent
 */
class PatientAppointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function serviceAppointment()
    {
        return $this->belongsTo(ServiceAppointment::class);
    }
}
