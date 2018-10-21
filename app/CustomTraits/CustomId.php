<?php

namespace App\CustomTraits;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

trait CustomId
{
    /**
     * Boot function from laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $key = $model->getKeyName();

            $id = strtoupper(Str::random(5));

            while (static::find($id) !== null) {
                $id = strtoupper(Str::random(5));
            }

            if (empty($model->{$key}))
                $model->{$key} = $id;
        });
    }
}