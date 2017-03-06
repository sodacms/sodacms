<?php

namespace Soda\Cms\Database\Blocks\Repositories;

use Soda\Cms\Database\Blocks\Interfaces\BlockTypeInterface;
use Soda\Cms\Database\Support\Repositories\AbstractRepository;
use Soda\Cms\Database\Support\Repositories\Traits\BuildsDataGrids;
use Soda\Cms\Database\Blocks\Interfaces\BlockTypeRepositoryInterface;

class BlockTypeRepository extends AbstractRepository implements BlockTypeRepositoryInterface
{
    use BuildsDataGrids;
    protected $model;

    public function __construct(BlockTypeInterface $model)
    {
        $this->model = $model;
    }

    public function getFields()
    {
        return app('soda.field.repository')->getAll();
    }

    public function getAvailableFields(BlockTypeInterface $blockType)
    {
        if (! $blockType->relationLoaded('fields')) {
            $blockType->load('fields');
        }

        return $this->getFields()->diff($blockType->getRelation('fields'));
    }

    public function getFilteredGrid($perPage)
    {
        $filter = $this->buildFilter($this->model);
        $grid = $this->buildGrid($filter);
        $grid = $this->addButtonsToGrid($grid, 'soda.block-types.edit', 'soda.block-types.destroy');
        $grid->paginate($perPage)->getGrid($this->getGridView());

        return compact('filter', 'grid');
    }
}
