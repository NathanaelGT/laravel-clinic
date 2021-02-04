<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

trait SoftDeletesCompare
{
    use SoftDeletes;

    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScopeCompare);
    }
}