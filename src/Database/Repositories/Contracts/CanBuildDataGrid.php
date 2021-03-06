<?php

namespace Soda\Cms\Database\Repositories\Contracts;

use Zofe\Rapyd\DataGrid\DataGrid;
use Zofe\Rapyd\DataFilter\DataFilter;

interface CanBuildDataGrid
{
    /**
     * @param int $perPage
     */
    public function getFilteredGrid($perPage);

    /**
     * @return DataFilter
     */
    public function buildFilter($model);

    /**
     * @return DataGrid
     */
    public function buildGrid(DataFilter $filter);

    /**
     * @return DataGrid
     */
    public function addButtonsToGrid(DataGrid $grid, $editRoute = null, $deleteRoute = null);

    /**
     * @return string
     */
    public function getGridView();
}
