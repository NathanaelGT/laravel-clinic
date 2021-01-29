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
        $this->hasMany(PatientAppoinment::class);
    }

    public function doctorWorktimes()
    {
        $this->belongsTo(DoctorWorktime::class);
    }
}
