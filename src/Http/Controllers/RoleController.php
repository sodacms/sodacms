<?php

namespace Soda\Cms\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Soda\Cms\Database\Roles\Interfaces\RoleRepositoryInterface;

class RoleController extends BaseController
{
    protected $roles;

    public function __construct(RoleRepositoryInterface $roles)
    {
        $this->roles = $roles;

        $this->middleware('soda.permission:view-roles');
        $this->middleware('soda.permission:create-roles')->only(['create', 'store']);
        $this->middleware('soda.permission:edit-roles')->only(['edit', 'update']);
        $this->middleware('soda.permission:delete-roles')->only(['delete']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return soda_cms_view('data.roles.index', $this->roles->getFilteredGrid(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $role = $this->roles->newInstance($request);
            $permissionIds = $this->roles->getPermissions();
        } catch (Exception $e) {
            return $this->handleException($e, trans('soda::errors.create', ['object' => 'role']));
        }

        return soda_cms_view('data.roles.view', compact('role', 'permissionIds'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $role = $this->roles->save($request);
        } catch (Exception $e) {
            return $this->handleException($e, trans('soda::errors.create', ['object' => 'role']));
        }

        return redirect()->route('soda.roles.edit', $role->getKey())->with('success', trans('soda::messages.created', ['object' => 'role']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = $this->roles->findById($id);
        $permissionIds = $this->roles->getPermissions();

        if (!$role) {
            return $this->handleError(trans('soda::errors.not-found', ['object' => 'role']));
        }

        return soda_cms_view('data.roles.view', compact('role', 'permissionIds'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $role = $this->roles->save($request, $id);
        } catch (Exception $e) {
            return $this->handleException($e, trans('soda::errors.update', ['object' => 'role']));
        }

        return redirect()->route('soda.roles.edit', $role->getKey())->with('success', trans('soda::messages.updated', ['object' => 'role']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->roles->destroy($id);
        } catch (ModelNotFoundException $e) {
            return $this->handleException($e, trans('soda::errors.not-found', ['object' => 'role']));
        } catch (Exception $e) {
            return $this->handleException($e, trans('soda::errors.delete', ['object' => 'role']));
        }

        return redirect()->route('soda.roles.index')->with('warning', trans('soda::messages.deleted', ['object' => 'role']));
    }
}