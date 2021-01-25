<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PatientAppoinment
 *
 * @property int $id
 * @property int|null $patient_id
 * @property int|null $doctor_service_id
 * @property string $day
 * @property string $time_start
 * @property string $time_end
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Patient|null $patient
 * @property-read \App\Models\DoctorService $service
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment query()
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereDoctorServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PatientAppoinment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PatientAppoinment extends Model
{
    use HasFactory;

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function service()
    {
        return $this->belongsTo(DoctorService::class);
    }
}
