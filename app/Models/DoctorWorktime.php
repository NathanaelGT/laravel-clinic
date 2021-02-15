<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\SoftDeletesCompare as SoftDeletes;
use Carbon\Carbon;

/**
 * App\Models\DoctorWorktime
 *
 * @property int $id
 * @property int|null $doctor_service_id
 * @property int $quota
 * @property string $day
 * @property string $time_start
 * @property string $time_end
 * @property string $active_date
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Conflict|null $conflict
 * @property-read \App\Models\DoctorService|null $doctorService
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceAppointment[] $serviceAppointment
 * @property-read int|null $service_appointment_count
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime newQuery()
 * @method static \Illuminate\Database\Query\Builder|DoctorWorktime onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime query()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereActiveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereDoctorServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereTimeStart($value)
 * @method static \Illuminate\Database\Query\Builder|DoctorWorktime withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DoctorWorktime withoutTrashed()
 * @mixin \Eloquent
 */
class DoctorWorktime extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['doctor_service_id', 'quota', 'day', 'time_start', 'time_end', 'active_date', 'deleted_at'];
    public $timestamps = false;

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function doctorService()
    {
        return $this->belongsTo(DoctorService::class);
    }

    public function serviceAppointment()
    {
        return $this->hasMany(ServiceAppointment::class);
    }

    public function conflict()
    {
        return $this->hasOne(Conflict::class);
    }
}
