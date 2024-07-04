<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountOpeningBalanceStoreRequest;
use App\Http\Requests\AccountOpeningBalanceUpdateRequest;
use App\Http\Resources\AccountOpeningBalanceCollection;
use App\Http\Resources\AccountOpeningBalanceResource;
use App\Models\AccountOpeningBalance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountOpeningBalanceController extends Controller
{
    public function index(Request $request): Response
    {
        $accountOpeningBalances = AccountOpeningBalance::all();

        return new AccountOpeningBalanceCollection($accountOpeningBalances);
    }

    public function store(AccountOpeningBalanceStoreRequest $request): Response
    {
        $accountOpeningBalance = AccountOpeningBalance::create($request->validated());

        return new AccountOpeningBalanceResource($accountOpeningBalance);
    }

    public function show(Request $request, AccountOpeningBalance $accountOpeningBalance): Response
    {
        return new AccountOpeningBalanceResource($accountOpeningBalance);
    }

    public function update(AccountOpeningBalanceUpdateRequest $request, AccountOpeningBalance $accountOpeningBalance): Response
    {
        $accountOpeningBalance->update($request->validated());

        return new AccountOpeningBalanceResource($accountOpeningBalance);
    }

    public function destroy(Request $request, AccountOpeningBalance $accountOpeningBalance): Response
    {
        $accountOpeningBalance->delete();

        return response()->noContent();
    }
}
