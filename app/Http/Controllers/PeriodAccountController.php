<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodAccountStoreRequest;
use App\Http\Requests\PeriodAccountUpdateRequest;
use App\Http\Resources\PeriodAccountCollection;
use App\Http\Resources\PeriodAccountResource;
use App\Models\PeriodAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeriodAccountController extends Controller
{
    public function index(Request $request)
    {
        $periodAccounts = PeriodAccount::all();

        return new PeriodAccountCollection($periodAccounts);
    }

    public function store(PeriodAccountStoreRequest $request)
    {
        $periodAccount = PeriodAccount::create($request->validated());

        return new PeriodAccountResource($periodAccount);
    }

    public function show(Request $request, PeriodAccount $periodAccount)
    {
        return new PeriodAccountResource($periodAccount);
    }

    public function update(PeriodAccountUpdateRequest $request, PeriodAccount $periodAccount)
    {
        $periodAccount->update($request->validated());

        return new PeriodAccountResource($periodAccount);
    }

    public function destroy(Request $request, PeriodAccount $periodAccount)
    {
        $periodAccount->delete();

        return response()->noContent();
    }
}
