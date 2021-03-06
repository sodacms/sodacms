<?php

namespace Soda\Cms\Foundation;

class DraftingHandler
{
    protected $draftables = [];

    /**
     * Registers an array of draftables.
     *
     * @param $draftables
     */
    public function registerDraftables($draftables)
    {
        foreach ($draftables as $draftable) {
            $this->registerDraftable($draftable);
        }
    }

    /**
     * Registers a new draftable.
     *
     * @param null $draftable
     */
    public function registerDraftable($draftable)
    {
        // Check if class uses Draftable trait
        if (method_exists($draftable, 'bootDraftable')) {
            $this->draftables[] = $draftable;
        }
    }

    /**
     * Returns a list of draftables that have been registered.
     *
     * @return array
     */
    public function getDraftables()
    {
        return $this->draftables;
    }
}
