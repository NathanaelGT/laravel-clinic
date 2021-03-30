<?php

namespace App\Models;

use App\Casts\TimeCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\SoftDeletesCompare as SoftDeletes;
use Carbon\Carbon;

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
