<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @mixin \Eloquent
 */
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
