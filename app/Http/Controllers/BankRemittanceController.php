<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRemittanceStoreRequest;
// use App\Http\Requests\BranchUpdateRequest;
use App\Http\Resources\BankRemittanceCollection;
use App\Http\Resources\BankRemittanceResource;
use App\Models\BankRemittance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankRemittanceController extends Controller
{
    public function index(Request $request): BankRemittanceCollection
    {
        $cashierExpenses = BankRemittance::all();

        return new BankRemittanceCollection($cashierExpenses);
    }

    public function store(BankRemittanceStoreRequest $request): BankRemittanceResource
    {
        $cashierExpenses = BankRemittance::create($request->validated());

        return new BankRemittanceResource($cashierExpenses);
    }

    public function show(Request $request, BankRemittance $cashierExpenses): BankRemittanceResource
    {
        return new BankRemittanceResource($cashierExpenses);
    }

    // public function update(BranchUpdateRequest $request, Branch $branch): BranchResource
    // {
    //     $branch->update($request->validated());

    //     return new BranchResource($branch);
    // }

    public function destroy($id)
    {
        // $branch->delete();
        BankRemittance::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
