<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DoctorService
 *
 * @property int $id
 * @property string $doctor_name
 * @property int|null $service_id
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PatientAppoinment[] $appoinment
 * @property-read int|null $appoinment_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DoctorWorktime[] $doctorWorktime
 * @property-read int|null $doctor_worktime_count
 * @property-read \App\Models\Service|null $service
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService query()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService whereDoctorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorService whereServiceId($value)
 * @mixin \Eloquent
 */
class DoctorService extends Model
{
    use HasFactory;

    public $fillable = ['doctor_name', 'service_id'];
    public $timestamps = false;

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function appoinment()
    {
        return $this->hasMany(PatientAppoinment::class);
    }

    public function doctorWorktime()
    {
        return $this->hasMany(DoctorWorktime::class);
    }
}
