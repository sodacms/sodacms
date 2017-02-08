<?php

namespace Soda\Cms\Database\Pages\Repositories;

use Illuminate\Http\Request;
use Soda\Cms\Database\Pages\Interfaces\PageTypeInterface;
use Soda\Cms\Database\Support\Repositories\AbstractRepository;
use Soda\Cms\Database\Support\Repositories\Traits\BuildsDataGrids;
use Soda\Cms\Database\Pages\Interfaces\PageTypeRepositoryInterface;

class PageTypeRepository extends AbstractRepository implements PageTypeRepositoryInterface
{
    use BuildsDataGrids;
    protected $model;

    public function __construct(PageTypeInterface $model)
    {
        $this->model = $model;
    }

    public function getBlockTypes()
    {
        return app('soda.block-type.repository')->getAll();
    }

    public function getAvailableBlockTypes(PageTypeInterface $pageType)
    {
        if (! $pageType->relationLoaded('block_types')) {
            $pageType->load('block_types');
        }

        return $this->getBlockTypes()->diff($pageType->getRelation('block_types'));
    }

    public function save(Request $request, $id = null)
    {
        $model = $id ? $this->model->findOrFail($id) : $this->newInstance();
        $model->fill($request->all());

        if ($model->id && $model->isDirty('allowed_children')) {
            $model->pages()->update(['allowed_children' => $this->model->allowed_children]);
        }

        $model->save();

        $allPageTypes = $request->input('subpage_types') ? array_keys($request->input('subpage_types')) : [];
        $restrictedPageTypes = array_keys(array_filter($request->input('subpage_types')));
        $isAllAllowed = $request->input('page_types_restricted') == '0' || $allPageTypes == $restrictedPageTypes;

        $model->subpage_types()->sync($isAllAllowed ? [] : $restrictedPageTypes);

        return $model;
    }

    public function getFilteredGrid($perPage)
    {
        $filter = $this->buildFilter($this->model);
        $grid = $this->buildGrid($filter);
        $grid = $this->addButtonsToGrid($grid, 'soda.page-types.edit', 'soda.page-types.destroy');
        $grid->paginate($perPage)->getGrid($this->getGridView());

        return compact('filter', 'grid');
    }

    public function getList($exceptId = false)
    {
        $pageTypes = $this->model;

        if ($exceptId !== false) {
            $pageTypes = $pageTypes->where('id', '!=', $exceptId);
        }

        return $pageTypes->get();
    }
}