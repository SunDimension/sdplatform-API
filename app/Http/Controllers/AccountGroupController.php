<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountGroupStoreRequest;
use App\Http\Requests\AccountGroupUpdateRequest;
use App\Http\Resources\AccountGroupCollection;
use App\Http\Resources\AccountGroupResource;
use App\Models\AccountGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountGroupController extends Controller
{
    public function index(Request $request)
    {
        $accountGroups = AccountGroup::all();

        return new AccountGroupCollection($accountGroups);
    }

    public function store(AccountGroupStoreRequest $request)
    {
        $accountGroup = AccountGroup::create($request->validated());

        return new AccountGroupResource($accountGroup);
    }
 
    public function show(Request $request, AccountGroup $accountGroup)
    {
        return new AccountGroupResource($accountGroup);
    }

    public function update(AccountGroupUpdateRequest $request, AccountGroup $accountGroup)
    {
        $accountGroup->update($request->validated());

        return new AccountGroupResource($accountGroup);
    }

    public function destroy(Request $request, AccountGroup $accountGroup)
    {
        $accountGroup->delete();

        return response()->noContent();
    }
}
