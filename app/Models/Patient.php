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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PatientAppoinment[] $appoinment
 * @property-read int|null $appoinment_count
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
    public $fillable = ['name', 'nik', 'phone_number', 'address'];
    public $timestamps = false;

    public function appoinment()
    {
        return $this->hasMany(PatientAppoinment::class);
    }
}
