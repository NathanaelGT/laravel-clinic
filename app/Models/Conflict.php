<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Conflict
 *
 * @property int $id
 * @property int $service_appointment_id
 * @property int $doctor_worktime_id
 * @property int $quota
 * @property string $time_start
 * @property string $time_end
 * @property-read \App\Models\DoctorWorktime $doctorWorktime
 * @property-read \App\Models\ServiceAppointment $serviceAppointment
 * @method static \Illuminate\Database\Eloquent\Builder|Conflict newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conflict newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conflict query()
 * @method static \Illuminate\Database\Eloquent\Builder|Conflict whereDoctorWorktimeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conflict whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conflict whereQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conflict whereServiceAppointmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conflict whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conflict whereTimeStart($value)
 * @mixin \Eloquent
 */
class Conflict extends Model
{
    use HasFactory;

    public $fillable = ['service_appointment_id', 'doctor_worktime_id', 'quota', 'time_start', 'time_end'];
    public $timestamps = false;

    public function serviceAppointment()
    {
        return $this->belongsTo(ServiceAppointment::class);
    }

    public function doctorWorktime()
    {
        return $this->belongsTo(DoctorWorktime::class);
    }
}
