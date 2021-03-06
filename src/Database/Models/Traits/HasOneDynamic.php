<?php

namespace Soda\Cms\Database\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Soda\Cms\Database\Models\Contracts\DynamicModelInterface;

trait HasOneDynamic
{
    public function hasOneDynamic(DynamicModelInterface $model, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();

        if ($this->content_type_id && ! $this->relationLoaded('type')) {
            $this->load('type');
        }

        if ($this->relationLoaded('type') && $this->type) {
            $instance = $this->newRelatedInstance(get_class($model))->fromTable($this->type->identifier);

            return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
        }

        // Empty/null relation
        return new MorphTo(
            $this->newQuery()->setEagerLoads([]), $this, null, null, null, null
        );
    }
}
