<?php

namespace App\Models;

use App\Casts\TimeCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentHistory extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date',
        'time_start' => TimeCast::class,
        'time_end' => TimeCast::class
    ];

    public $guarded = ['created_at', 'updated_at'];

    public function doctorService()
    {
        return $this->belongsTo(DoctorService::class);
    }

    public function doctorWorktime()
    {
        return $this->belongsTo(DoctorWorktime::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function reschedule()
    {
        return $this->hasOne(AppointmentHistory::class, 'reschedule_id');
    }
}
