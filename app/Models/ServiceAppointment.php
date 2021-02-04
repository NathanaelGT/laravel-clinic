<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceAppointment
 *
 * @property int $id
 * @property int|null $doctor_worktime_id
 * @property string $date
 * @property string $quota
 * @property-read \App\Models\Conflict|null $conflict
 * @property-read \App\Models\DoctorWorktime|null $doctorWorktime
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PatientAppointment[] $patientAppointment
 * @property-read int|null $patient_appointment_count
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAppointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAppointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAppointment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAppointment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAppointment whereDoctorWorktimeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAppointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAppointment whereQuota($value)
 * @mixin \Eloquent
 */
class ServiceAppointment extends Model
{
    use HasFactory;
    public $fillable = ['doctor_worktime_id', 'date', 'quota'];
    public $timestamps = false;

    public function getQuotaAttribute($value)
    {
        return explode(',', $value);
    }

    public function setQuotaAttribute($value)
    {
        $this->attributes['quota'] = implode(',', $value);
    }

    public function patientAppointment()
    {
        return $this->hasMany(PatientAppointment::class);
    }

    public function doctorWorktime()
    {
        return $this->belongsTo(DoctorWorktime::class);
    }

    public function conflict()
    {
        return $this->hasOne(Conflict::class);
    }
}
