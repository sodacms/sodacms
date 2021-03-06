<?php

namespace Soda\Cms\Database\Models;

use Carbon\Carbon;
use Exception;
use Soda\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Soda\Cms\Database\Models\Traits\Auditable;
use Soda\Cms\Database\Models\Traits\Draftable;
use Soda\Cms\Database\Models\Traits\Sluggable;
use Soda\Cms\Database\Observers\ContentObserver;
use Soda\Cms\Database\Models\Traits\HasOneDynamic;
use Soda\Cms\Database\Models\Traits\SortableClosure;
use Soda\Cms\Database\Models\Contracts\ContentInterface;
use Soda\Cms\Database\Models\Traits\HasDefaultAttributes;
use Soda\Cms\Database\Models\Contracts\BlockTypeInterface;
use Soda\Cms\Database\Models\Traits\AdditionalClosureScopes;
use Soda\Cms\Database\Models\Traits\OptionallyBoundToApplication;

class Content extends Entity implements ContentInterface
{
    use Auditable, SoftDeletes, Sluggable, Draftable, OptionallyBoundToApplication, HasDefaultAttributes, AdditionalClosureScopes, SortableClosure, HasOneDynamic;

    protected static $sortableGroupField = ['application_id', 'parent_id'];
    public $timestamps = true;
    public $fillable = [
        'name',
        'identifier',
        'slug',
        'parent_id',
        'position',
        'application_id',
        'content_type_id',
        'view_action',
        'view_action_type',
        'edit_action',
        'edit_action_type',
        'status',
        'is_sluggable',
        'is_folder',
        'is_movable',
        'is_publishable',
        'is_deletable',
        'published_at',
    ];
    protected $dates = ['created_at', 'updated_at', 'published_at', 'deleted_at'];
    protected $table = 'content';
    protected $isSlugToggled = true;
    protected $defaults = [
        'view_action'      => '',
        'view_action_type' => 'view',
        'edit_action'      => 'soda::data.content.view',
        'edit_action_type' => 'view',
    ];

    /**
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [
        'application_id',
        'view_action',
        'view_action_type',
        'edit_action',
        'edit_action_type',
        'is_publishable',
        'is_sluggable',
        'is_folder',
        'is_movable',
        'is_deletable',
    ];

    /**
     * ClosureTable model instance.
     *
     * @var pagesClosure
     */
    protected $closure = ContentClosure::class;

    public static function boot()
    {
        static::observe(ContentObserver::class);

        parent::boot();
    }

    public function properties()
    {
        return $this->hasOneDynamic($this->getDynamicModel());
    }

    public function getDynamicModel()
    {
        return new DynamicContent;
    }

    public function type()
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function blockTypes()
    {
        return $this->belongsToMany(BlockType::class, 'content_block_types')->withPivot('min_blocks', 'max_blocks', 'is_orderable');
    }

    public function getBlock($identifier)
    {
        return $this->block($identifier)->get();
    }

    public function block($identifier)
    {
        $block = $identifier instanceof BlockTypeInterface ? $identifier : $this->getBlockType($identifier);

        if ($block) {
            return $block->blockQuery($this->getKey());
        }

        throw new Exception('Page does not have block: \''.$identifier.'\'.');
    }

    public function getBlockType($identifier)
    {
        $block = $this->blockTypes->filter(function ($item) use ($identifier) {
            return $item->identifier == $identifier;
        })->first();

        if (! $block && $this->type && $this->type->blockTypes) {
            $block = $this->type->blockTypes->filter(function ($item) use ($identifier) {
                return $item->identifier == $identifier;
            })->first();
        }

        return $block;
    }

    public function newBlock($identifier)
    {
        return DynamicBlock::fromTable($identifier, ['page_id' => $this->id]);
    }

    public function isSluggable()
    {
        return $this->is_sluggable;
    }

    public function isDeletable()
    {
        return $this->is_deletable;
    }

    public function isFolder()
    {
        return $this->is_folder;
    }

    public function isMovable()
    {
        return $this->is_movable;
    }

    public function getPropertiesAttribute($value)
    {
        $relatedModel = $this->getRelationValue('properties');

        if (! $relatedModel) {
            $relatedModel = new DynamicContent;

            if ($this->type) {
                $relatedModel->setPrefixedTable($this->type->identifier);
            }
        }

        return $relatedModel;
    }

    /**
     * {@inheritdoc}
     */
    public function readyForAuditing()
    {
        if ($this->isEventAuditable($this->auditEvent)) {
            if ($this->auditEvent !== 'updated' || $this->isDirty() || ($this->shouldDynamicTableExist() && $this->properties->isDirty())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the old/new attributes corresponding to a created event.
     *
     * @param array $old
     * @param array $new
     *
     * @return void
     */
    protected function auditCreatedAttributes(array &$old, array &$new)
    {
        foreach ($this->attributes as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $new[$attribute] = $value;
            }
        }

        if ($this->shouldDynamicTableExist()) {
            foreach ($this->properties->getAttributes() as $attribute => $value) {
                if (! in_array($attribute, $this->auditableExclusions)) {
                    $new[$this->type->identifier][$attribute] = $value;
                }
            }
        }
    }

    /**
     * Set the old/new attributes corresponding to an updated event.
     *
     * @param array $old
     * @param array $new
     *
     * @return void
     */
    protected function auditUpdatedAttributes(array &$old, array &$new)
    {
        foreach ($this->getDirty() as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $old[$attribute] = array_get($this->original, $attribute);
                $new[$attribute] = array_get($this->attributes, $attribute);
            }
        }

        if ($this->shouldDynamicTableExist()) {
            foreach ($this->properties->getDirty() as $attribute => $value) {
                if (! in_array($attribute, $this->auditableExclusions)) {
                    $old[$this->type->identifier][$attribute] = array_get($this->properties->getOriginal(), $attribute);
                    $new[$this->type->identifier][$attribute] = array_get($this->properties->getAttributes(), $attribute);
                }
            }
        }
    }

    /**
     * Set the old/new attributes corresponding to a deleted event.
     *
     * @param array $old
     * @param array $new
     *
     * @return void
     */
    protected function auditDeletedAttributes(array &$old, array &$new)
    {
        foreach ($this->attributes as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $old[$attribute] = $value;
            }
        }

        if ($this->shouldDynamicTableExist()) {
            foreach ($this->properties->getAttributes() as $attribute => $value) {
                if (! in_array($attribute, $this->auditableExclusions)) {
                    $old[$this->type->identifier][$attribute] = $value;
                }
            }
        }
    }
}
