<?php

namespace App\Models;

use App\Casts\TimeCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AppointmentHistory
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property string $doctor
 * @property string $service
 * @property int|null $doctor_service_id
 * @property string $time_start
 * @property string $time_end
 * @property string $patient_name
 * @property string $patient_nik
 * @property string $patient_phone_number
 * @property string $patient_address
 * @property int|null $patient_id
 * @property string $status
 * @property int|null $reschedule_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DoctorService|null $doctorService
 * @property-read \App\Models\Patient|null $patient
 * @property-read AppointmentHistory|null $reschedule
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory with(array|string $relations)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereDoctor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereDoctorServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereDoctorWorktimeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory wherePatientAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory wherePatientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory wherePatientNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory wherePatientPhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereRescheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
