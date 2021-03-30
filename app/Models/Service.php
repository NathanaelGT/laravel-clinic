<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $guarded = ['deleted_at'];
    public $timestamps = false;

    public function doctorService()
    {
        return $this->hasMany(DoctorService::class);
    }
}
