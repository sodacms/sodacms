<?php

namespace Soda\Cms\Database\Repositories;

use Illuminate\Http\Request;
use Soda\Cms\Support\Facades\Soda;

abstract class AbstractRepository
{
    protected $model;

    public function getAll()
    {
        return $this->model->all();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param int $id
     */
    public function save(Request $request, $id = null)
    {
        $model = $id ? $this->model->findOrFail($id) : $this->newInstance();
        $model->fill($request->all())->save();

        return $model;
    }

    public function newInstance($attributes = [])
    {
        return $this->model->newInstance($attributes)->fill([
            'application_id' => Soda::getApplication()->id,
        ]);
    }

    public function destroy($id)
    {
        $block = $this->model->find($id);

        if ($block) {
            $block->delete();
        }

        return $block;
    }
}
