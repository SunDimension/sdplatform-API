<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionsStoreRequest;
use App\Http\Requests\PermissionsUpdateRequest;
use App\Http\Resources\PermissionCollection;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionsController extends Controller
{
    public function index(Request $request): PermissionCollection
    {
        $permissions = Permission::all();

        return new PermissionCollection($permissions);
    }

    public function store(PermissionsStoreRequest $request): PermissionResource
    {
        $permission = Permission::create($request->validated());

        return new PermissionResource($permission);
    }

    public function show(Request $request, Permission $permission): PermissionResource
    {
        return new PermissionResource($permission);
    }

    public function update(PermissionsUpdateRequest $request, Permission $permission): PermissionResource
    {
        $permission->update($request->validated());

        return new PermissionResource($permission);
    }

    public function destroy(Request $request, Permission $permission): Response
    {
        $permission->delete();

        return response()->noContent();
    }
}
