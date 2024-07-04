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
    public function index(Request $request): Response
    {
        $accounts = Account::all();

        return new AccountCollection($accounts);
    }

    public function store(AccountStoreRequest $request): Response
    {
        $account = Account::create($request->validated());

        return new AccountResource($account);
    }

    public function show(Request $request, Account $account): Response
    {
        return new AccountResource($account);
    }

    public function update(AccountUpdateRequest $request, Account $account): Response
    {
        $account->update($request->validated());

        return new AccountResource($account);
    }

    public function destroy(Request $request, Account $account): Response
    {
        $account->delete();

        return response()->noContent();
    }
}
