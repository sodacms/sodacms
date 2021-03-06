<?php

namespace Soda\Cms\Database\Models\Traits;

trait Sluggable
{
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value === null ? $value : $this->fixSlug($value);

        return $this;
    }

    /**
     * Fixes slashes used in slug.
     *
     * @param $slug
     *
     * @return string
     */
    protected function fixSlug($slug)
    {
        $parts = explode('/', $slug);
        $slug = '';
        foreach ($parts as $part) {
            if ($part) {
                $slug .= '/'.str_slug($part);
            }
        }

        return strtolower('/'.ltrim($slug, '/'));
    }

    /**
     * Takes a parent tree item and generates a slug based off it.
     *
     * @param $title
     * @param $useParent
     *
     * @return string
     */
    public function generateSlug($title, $useParent = true)
    {
        $slug = $useParent ? $this->fixSlug($this->fixSlug($this->getAttribute('slug')).$this->fixSlug($title)) : $this->fixSlug($title);

        // Make sure it doesn't already exist. Exclude own record if we're not using parent to generate slug
        if ($this->getExistingSlug($slug, ! $useParent)) {
            // It already exists, increment it
            $slug = $this->incrementLatestSlug($slug);
        }

        return $slug;
    }

    /**
     * Checks where slug is currently being used.
     *
     * @param string $slug
     * @param        $excludeSelf
     *
     * @return bool
     */
    protected function getExistingSlug($slug, $excludeSelf = false)
    {
        $existing = static::where('slug', "$slug");

        if (isset($this->isSlugToggled) && $this->isSlugToggled == true) {
            $existing->where('is_sluggable', true);
        }

        if ($excludeSelf) {
            $existing->where('id', '!=', $this->id);
        }

        return $existing->first() ? true : false;
    }

    /**
     * Increments the number appended to the slug, to prevent duplicates.
     *
     * @param string $slug
     *
     * @return string
     */
    protected function incrementLatestSlug($slug)
    {
        $highest = static::where('slug', 'like', "$slug-%")->orderBy('slug', 'desc')->first();
        $num = 1;

        if ($highest) {
            $num = str_replace("$slug-", '', $highest->getAttribute('slug'));
            $num++;
        }

        return $slug.'-'.$num;
    }
}
