<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolesStoreRequest;
use App\Http\Requests\RolesUpdateRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RolesController extends Controller
{
    public function index(Request $request): RoleCollection
    {
        $roles = Role::all();

        return new RoleCollection($roles);
    }

    public function store(RolesStoreRequest $request): RoleResource
    {
        $role = Role::create($request->validated());

        return new RoleResource($role);
    }

    public function show(Request $request, Role $role): RoleResource
    {
        return new RoleResource($role);
    }

    public function update(RolesUpdateRequest $request, Role $role): RoleResource
    {
        $role->update($request->validated());

        return new RoleResource($role);
    }

    public function destroy(Request $request, Role $role): Response
    {
        $role->delete();

        return response()->noContent();
    }
}
