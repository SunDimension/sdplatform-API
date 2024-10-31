<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Requests\AttachPermissionRequest;
use App\Http\Requests\DetachPermissionRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Import the ModelNotFoundException
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index(Request $request): RoleCollection
    {
        $roles = Role::with(['permissions'])->get();
        return new RoleCollection($roles);
    }

    public function store(RoleStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $role = Role::create($request->validated());

            // Attach the permissions if provided
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->input('permissions', []));
            }

            DB::commit();
            return new JsonResponse(new RoleResource($role), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create role'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Role $role): RoleResource
    {
        return new RoleResource($role->load(['permissions']));
    }

    public function update(RoleUpdateRequest $request, Role $role): JsonResponse
    {
        DB::beginTransaction();
        try {
            $role->update($request->validated());
            $role->permissions()->sync($request->input('permissions', []));
            DB::commit();
            return new JsonResponse(new RoleResource($role), Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update role'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function attachPermission(AttachPermissionRequest $request, Role $role): JsonResponse
    {
        $permission = Permission::findOrFail($request->input('permission_id'));

        if (!$role->permissions->contains($permission)) {
            $role->permissions()->attach($permission);
        }

        return response()->json(['message' => 'Permission attached successfully.'], Response::HTTP_OK);
    }

    public function detachPermission(DetachPermissionRequest $request, Role $role): JsonResponse
    {
        $permission = Permission::findOrFail($request->input('permission_id'));

        if ($role->permissions->contains($permission)) {
            $role->permissions()->detach($permission);
            return response()->json(['message' => 'Permission detached successfully.'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Permission was not attached.'], Response::HTTP_OK);
    }
}
