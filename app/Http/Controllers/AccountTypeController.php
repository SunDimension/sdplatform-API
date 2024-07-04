<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountTypeStoreRequest;
use App\Http\Requests\AccountTypeUpdateRequest;
use App\Http\Resources\AccountTypeCollection;
use App\Http\Resources\AccountTypeResource;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $accountTypes = AccountType::all();

        return new AccountTypeCollection($accountTypes);
    }

    public function store(AccountTypeStoreRequest $request): Response
    {
        $accountType = AccountType::create($request->validated());

        return new AccountTypeResource($accountType);
    }

    public function show(Request $request, AccountType $accountType): Response
    {
        return new AccountTypeResource($accountType);
    }

    public function update(AccountTypeUpdateRequest $request, AccountType $accountType): Response
    {
        $accountType->update($request->validated());

        return new AccountTypeResource($accountType);
    }

    public function destroy(Request $request, AccountType $accountType): Response
    {
        $accountType->delete();

        return response()->noContent();
    }
}
