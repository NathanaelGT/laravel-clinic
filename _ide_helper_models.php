<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AppointmentHistory
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property string $doctor
 * @property string $service
 * @property int|null $doctor_service_id
 * @property mixed $time_start
 * @property mixed $time_end
 * @property int|null $doctor_worktime_id
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
 * @property-read \App\Models\DoctorWorktime|null $doctorWorktime
 * @property-read \App\Models\Patient|null $patient
 * @property-read AppointmentHistory|null $reschedule
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
 */
	class AppointmentHistory extends \Eloquent {}
}

namespace App\Models{
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
 */
	class DoctorService extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DoctorWorktime
 *
 * @property int $id
 * @property int|null $doctor_service_id
 * @property int $quota
 * @property string $day
 * @property mixed $time_start
 * @property mixed $time_end
 * @property int|null $replaced_with_id
 * @property \Illuminate\Support\Carbon|null $active_date
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AppointmentHistory[] $appointmentHistory
 * @property-read int|null $appointment_history_count
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
 */
	class DoctorWorktime extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Patient
 *
 * @property int $id
 * @property string $name
 * @property string $nik
 * @property string $phone_number
 * @property string $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AppointmentHistory[] $appointmentHistory
 * @property-read int|null $appointment_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Patient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Patient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Patient query()
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Patient wherePhoneNumber($value)
 */
	class Patient extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Service
 *
 * @property int $id
 * @property string $name
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DoctorService[] $doctorService
 * @property-read int|null $doctor_service_count
 * @method static \Illuminate\Database\Eloquent\Builder|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newQuery()
 * @method static \Illuminate\Database\Query\Builder|Service onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereName($value)
 * @method static \Illuminate\Database\Query\Builder|Service withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Service withoutTrashed()
 */
	class Service extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int $is_valid
 * @property string|null $token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsValid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

