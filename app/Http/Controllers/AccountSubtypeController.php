<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountSubtypeStoreRequest;
use App\Http\Requests\AccountSubtypeUpdateRequest;
use App\Http\Resources\AccountSubtypeCollection;
use App\Http\Resources\AccountSubtypeResource;
use App\Models\AccountSubtype;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountSubtypeController extends Controller
{
    public function index(Request $request): Response
    {
        $accountSubtypes = AccountSubtype::all();

        return new AccountSubtypeCollection($accountSubtypes);
    }

    public function store(AccountSubtypeStoreRequest $request): Response
    {
        $accountSubtype = AccountSubtype::create($request->validated());

        return new AccountSubtypeResource($accountSubtype);
    }

    public function show(Request $request, AccountSubtype $accountSubtype): Response
    {
        return new AccountSubtypeResource($accountSubtype);
    }

    public function update(AccountSubtypeUpdateRequest $request, AccountSubtype $accountSubtype): Response
    {
        $accountSubtype->update($request->validated());

        return new AccountSubtypeResource($accountSubtype);
    }

    public function destroy(Request $request, AccountSubtype $accountSubtype): Response
    {
        $accountSubtype->delete();

        return response()->noContent();
    }
}
