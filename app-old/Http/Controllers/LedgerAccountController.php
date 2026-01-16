<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountStoreRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Http\Resources\LedgerAccountCollection;
use App\Http\Resources\LedgerAccountResource;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LedgerAccountController extends Controller
{
    // public function index(Request $request)
    // {
    //     $accounts = LedgerAccount::all();

    //     return new LedgerAccountCollection($accounts);
    // }


    public function index(Request $request)
    {
        $accounts = LedgerAccount::where('account_type_id', '2b80752d-d035-11f0-be14-d067e5517519')->get();

        return new LedgerAccountCollection($accounts);
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
