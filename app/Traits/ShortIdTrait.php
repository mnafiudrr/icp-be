<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait ShortIdTrait
{
    /**
     * Boot function to generate short id before saving the model
     */
    public static function boot()
    {
        parent::boot();
        self::generateId();
    }

    public static function generateId()
    {
        static::creating(function ($model) {
            $model->id = self::generateShortId();
            $model->keyType = 'string';
            if (Schema::hasColumn($model->getTable(), 'created_by') && auth()->check())
                $model->created_by = auth()->id();
        });
    }

    /**
     * Generate a unique short id
     *
     * @return string
     */
    private static function generateShortId()
    {
        $id = Str::random(6);

        if (self::checkIdExist($id))
            return self::generateShortId();

        return $id;
    }

    /**
     * Get the model by short id
     *
     * @param string $id
     * @return Model
     */
    private static function checkIdExist($id)
    {
        $model = self::where('id', $id)->first();

        if ($model)
            return true;

        return false;
    }
}