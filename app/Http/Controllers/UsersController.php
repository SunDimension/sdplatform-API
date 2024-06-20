<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsersController extends Controller
{
    public function index(Request $request): UserCollection
    {
        $users = User::all();

        return new UserCollection($users);
    }

    public function store(UserStoreRequest $request): UserResource
    {
        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles',[]));
        $user->branch()->sync($request->input('branch',[]));
        $user->warehouse()->sync($request->input('warehouse',[]));

        return new UserResource($user);
    }

    public function show(Request $request, User $user): UserResource
    {
        return new UserResource($user->load(['roles','branch','warehouse']));
    }

    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $user->update($request->all());
        
        $user->roles()->sync($request->input('roles',[]));
        $user->branch()->sync($request->input('branch',[]));
        $user->warehouse()->sync($request->input('warehouse',[]));

        return new UserResource($user);
    }

    public function destroy(Request $request, User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }
}
