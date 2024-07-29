<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountStoreRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Http\Resources\AccountCollection;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::all();

        return new AccountCollection($accounts);
    }

    public function store(AccountStoreRequest $request)
    {
        $account = Account::create($request->validated());

        return new AccountResource($account);
    }

    public function show(Request $request, Account $account)
    {
        return new AccountResource($account);
    }

    public function update(AccountUpdateRequest $request, Account $account)
    {
        $account->update($request->validated());

        return new AccountResource($account);
    }

    public function destroy(Request $request, Account $account)
    {
        $account->delete();

        return response()->noContent();
    }
}
