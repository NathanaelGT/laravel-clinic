<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\DoctorService
 *
 * @property int $id
 * @property string $doctor_name
 * @property int|null $service_id
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AppointmentHistory[] $appointmentHistory
 * @property-read int|null $appointment_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DoctorWorktime[] $doctorWorktime
 * @property-read int|null $doctor_worktime_count
 * @property-read \App\Models\Service|null $service
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService newQuery()
 * @method static \Illuminate\Database\Query\Builder|DoctorService onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService query()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService whereDoctorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService whereServiceId($value)
 * @method static \Illuminate\Database\Query\Builder|DoctorService withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DoctorService withoutTrashed()
 * @mixin \Eloquent
 */
class DoctorService extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $guarded = ['deleted_at'];
    public $timestamps = false;

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function appointmentHistory()
    {
        return $this->hasMany(AppointmentHistory::class);
    }

    public function doctorWorktime()
    {
        return $this->hasMany(DoctorWorktime::class);
    }
}
