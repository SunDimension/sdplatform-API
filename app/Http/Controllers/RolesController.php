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
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index(Request $request): RoleCollection
    {
        return new RoleCollection(Role::with(['permissions'])->get());
    }

    public function store(RoleStoreRequest $request): RoleResource
    {
         DB::beginTransaction();
    try {
        $role = Role::create($request->validated());

        // Attach the permissions if provided
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions', []));
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }

    return new RoleResource($role);
    }

    public function show(Request $request, Role $role): RoleResource
    {
        return new RoleResource($role->load(['permissions']));
    }

    public function update(RoleUpdateRequest $request, Role $role): RoleResource
    {
       DB::beginTransaction();
    try {
        $role->update($request->validated());
        $role->permissions()->sync($request->input('permissions', []));
        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
        return new RoleResource($role);
    }

    public function destroy($id): JsonResponse
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        $role->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
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

        if (!$role->permissions->contains($permission)) {
            return response()->json(['message' => 'Permission was not attached.'], Response::HTTP_OK);
        }

        $role->permissions()->detach($permission);

        return response()->json(['message' => 'Permission detached successfully.'], Response::HTTP_OK);
    }
}
