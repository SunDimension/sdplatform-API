<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartOfAccountStoreRequest;
use App\Http\Requests\ChartOfAccountUpdateRequest;
use App\Http\Resources\ChartOfAccountResource;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        $accounts = ChartOfAccount::with('accountType')->get();
        return ChartOfAccountResource::collection($accounts);
    }

    public function store(ChartOfAccountStoreRequest $request)
    {
        $account = ChartOfAccount::create($request->validated());
        return new ChartOfAccountResource($account);
    }

    public function show(ChartOfAccount $chart_of_account)
    {
        return new ChartOfAccountResource($chart_of_account->load('accountType'));
    }

    public function update(ChartOfAccountUpdateRequest $request, ChartOfAccount $chart_of_account)
    {
        $chart_of_account->update($request->validated());
        return new ChartOfAccountResource($chart_of_account);
    }

    public function destroy(ChartOfAccount $chart_of_account)
    {
        $chart_of_account->delete();
        return response()->noContent();
    }
}
