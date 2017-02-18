<?php

namespace Soda\Cms\Database\Support\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Rutorika\Sortable\MorphToSortedManyTrait;

trait MorphToSortedMany
{
    use MorphToSortedManyTrait;

    /**
     * Get the relationship name of the belongs to many.
     *
     * @return string
     */
    protected function getBelongsToManyCaller()
    {
        $self = __FUNCTION__;

        $caller = Arr::first(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), function ($trace) use ($self) {
            $caller = $trace['function'];

            return ! in_array($caller, Model::$manyMethods) && $caller != $self;
        });

        return ! is_null($caller) ? $caller['function'] : null;
    }
}