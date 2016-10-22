<?php

namespace Soda\Cms\Database\Support\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait OptionallyBoundToApplication
{
    /**
     * Adds application_id global scope filter to model.
     */
    public static function bootOptionallyBoundToApplication()
    {
        static::addGlobalScope('in-application', function (Builder $builder) {
            $builder->where('application_id', '=', app('soda')->getApplication()->getKey())
                ->orWhereNull('application_id')
                ->orWhere('application_id', '=', '');
        });
    }
}
