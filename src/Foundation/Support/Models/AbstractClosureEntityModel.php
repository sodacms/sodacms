<?php

namespace Soda\Cms\Foundation\Support\Models;

use Franzose\ClosureTable\Models\Entity;

abstract class AbstractClosureEntityModel extends Entity
{
    /**
     * Retrieves query builder for root (with no ancestors) models.
     *
     * @return \Franzose\ClosureTable\Extensions\Collection
     */
    public static function collectRoots()
    {
        /**
         * @var Entity $instance
         */
        $instance = new static;

        return $instance->whereNull($instance->getParentIdColumn());
    }

    /**
     * Retrieves query builder for all descendants of a model.
     *
     * @return \Franzose\ClosureTable\Extensions\QueryBuilder
     */
    public function collectDescendants($withSelf = false)
    {
        return $this->joinClosureBy('descendant', $withSelf);
    }

    /**
     * Retrieves query builder for all ancestors of a model.
     *
     * @return Collection
     */
    public function collectAncestors()
    {
        return $this->joinClosureBy('ancestor');
    }

    /**
     * Retrieves query builder for all children of a model.
     *
     * @return \Franzose\ClosureTable\Extensions\Collection
     */
    public function collectChildren()
    {
        if ($this->hasChildrenRelation()) {
            $result = $this->getRelation($this->getChildrenRelationIndex());
        } else {
            $result = $this->children();
        }

        return $result;
    }

    /**
     * Retrieves query builder for children within given positions range.
     *
     * @param int $from
     * @param int $to
     *
     * @return Collection
     */
    public function collectChildrenRange($from, $to = null)
    {
        return $this->children([$from, $to]);
    }

    /**
     * Retrieves query builder for neighbours of a model.
     *
     * @return \Franzose\ClosureTable\Extensions\Collection
     */
    public function collectNeighbors()
    {
        return $this->siblings(static::QUERY_NEIGHBORS);
    }

    /**
     * Retrives query builder for all siblings of a model.
     *
     * @return \Franzose\ClosureTable\Extensions\Collection
     */
    public function collectSiblings()
    {
        return $this->siblings(static::QUERY_ALL);
    }

    /**
     * Retrieves query builder for previous siblings of a model.
     *
     * @return \Franzose\ClosureTable\Extensions\Collection
     */
    public function collectPrevSiblings()
    {
        return $this->siblings(static::QUERY_PREV_ALL);
    }

    /**
     * Retrieves query builder for next siblings of a model.
     *
     * @return \Franzose\ClosureTable\Extensions\Collection
     */
    public function collectNextSiblings()
    {
        return $this->siblings(static::QUERY_NEXT_ALL);
    }

    /**
     * Retrieves query builder for siblings in a certain range of a model.
     *
     * @param int $from
     * @param int $to
     *
     * @return Collection
     */
    public function collectSiblingsRange($from, $to = null)
    {
        return $this->siblings([$from, $to]);
    }

    /*
     * get tree from given tree.
     */
    public function buildTree($id = null)
    {
        $treeModel = isset($id) && $id && $id !== '#' ? $this->find($id) : $this->getRoots()->first();

        // we need to get elements for each element in the tree.
        // TODO: see https://github.com/franzose/ClosureTable/issues/164
        return $treeModel ? $treeModel->collectDescendants(true)->orderBy('position')->get()->toTree() : [];
    }
}