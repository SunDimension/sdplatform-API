<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function index(Request $request): UserCollection
    {
        $users = User::with('roles')->get();

        return new UserCollection($users);
    }

    public function store(UserStoreRequest $request): UserResource
    {
        $user = User::create($request->validated());

        return new UserResource($user);
    }

    public function show(Request $request, User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return new UserResource($user);
    }

    public function destroy($id)
    {

        User::destroy($id);


        return response(null, Response::HTTP_NO_CONTENT);
    }

    //  public function assignRole(Request $request, User $user): JsonResponse
    //     {
    //         $role = Role::findOrFail($request->input('role_id'));

    //         if (!$user->roles->contains($role)) {
    //             $user->roles()->attach($role);
    //         }

    //         return response()->json(['message' => 'Role assigned successfully.'], Response::HTTP_OK);
    //     }

    public function assignRole(Request $request, User $user): JsonResponse
    {
        $roleIds = $request->input('roles'); // expecting an array of role IDs

        // Sync roles, which will attach the roles that are not already attached
        $user->roles()->syncWithoutDetaching($roleIds);

        return response()->json(['message' => 'Roles assigned successfully.'], Response::HTTP_OK);
    }

    public function removeRole(Request $request, User $user): JsonResponse
    {
        $role = Role::findOrFail($request->input('role_id'));

        if ($user->roles->contains($role)) {
            $user->roles()->detach($role);
        }

        return response()->json(['message' => 'Role removed successfully.'], Response::HTTP_OK);
    }

    public function syncRoles(Request $request, User $user): JsonResponse
    {
        // Retrieve role_ids from the request (this will replace all current roles)
        $roleIds = $request->input('role_ids');

        // Sync roles, removing old ones and assigning the new set
        $user->roles()->sync($roleIds);

        return response()->json(['message' => 'Roles synced successfully.'], Response::HTTP_OK);
    }


    public function getUserDetails(Request $request)
    {
        // Assuming you're using Auth for user authentication
        //  $userId = Auth::id();
        $user = User::with(['branch', 'store'])->find(Auth::id()); // Eager load branch and store

        if ($user) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'branch' => $user->branch, // Include branch details
                'store' => $user->store,   // Include store details
            ]);
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    }
}
