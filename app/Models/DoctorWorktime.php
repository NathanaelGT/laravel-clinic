<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DoctorWorktime
 *
 * @property int $id
 * @property int|null $doctor_service_id
 * @property int $quota
 * @property string $day
 * @property string $time_start
 * @property string $time_end
 * @property string|null $deleted_at
 * @property-read \App\Models\DoctorService|null $doctorService
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime query()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereDoctorServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereTimeStart($value)
 * @mixin \Eloquent
 */
class DoctorWorktime extends Model
{
    use HasFactory;

    public $fillable = ['doctor_service_id', 'quota', 'day', 'time_start', 'time_end'];

    public function doctorService()
    {
        return $this->belongsTo(DoctorService::class);
    }
}
