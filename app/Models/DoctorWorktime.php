<?php

namespace App\Models;

use App\Casts\TimeCast;
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
 * @property int|null $replaced_with_id
 * @property \Illuminate\Support\Carbon $active_date
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\DoctorService|null $doctorService
 * @property-read DoctorWorktime|null $replacedWith
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime query()
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereActiveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereDoctorServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereReplacedWithId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DoctorWorktime whereTimeStart($value)
 * @mixin \Eloquent
 */
class DoctorWorktime extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'active_date' => 'date',
        'time_start' => TimeCast::class,
        'time_end' => TimeCast::class
    ];

    public $guarded = [];
    public $timestamps = false;

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public static function hasConflict(): bool
    {
        return DoctorWorktime::whereNotNull('replaced_with_id')->whereNull('deleted_at')->exists();
    }

    public function doctorService()
    {
        return $this->belongsTo(DoctorService::class);
    }

    public function appointmentHistory()
    {
        return $this->hasMany(AppointmentHistory::class);
    }

    public function replacedWith()
    {
        return $this->hasOne(DoctorWorktime::class, 'id', 'replaced_with_id');
    }
}
