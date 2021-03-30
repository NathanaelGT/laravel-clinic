<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    public $guarded = [];
    public $timestamps = false;

    public function appointmentHistory()
    {
        return $this->hasMany(AppointmentHistory::class);
    }
}
