<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
