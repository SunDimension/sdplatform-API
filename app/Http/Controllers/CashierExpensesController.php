<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashierExpenseStoreRequest;
// use App\Http\Requests\BranchUpdateRequest;
use App\Http\Resources\CashierExpensesCollection;
use App\Http\Resources\CashierExpensesResource;
use App\Models\CashierExpenses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CashierExpensesController extends Controller
{
    public function index(Request $request): CashierExpensesCollection
    {
        $cashierExpenses = CashierExpenses::all();

        return new CashierExpensesCollection($cashierExpenses);
    }

    public function store(CashierExpenseStoreRequest $request): CashierExpensesResource
    {
        $cashierExpenses = CashierExpenses::create($request->validated());

        return new CashierExpensesResource($cashierExpenses);
    }

    public function show(Request $request, CashierExpenses $cashierExpenses): CashierExpensesResource
    {
        return new CashierExpensesResource($cashierExpenses);
    }

    // public function update(BranchUpdateRequest $request, Branch $branch): BranchResource
    // {
    //     $branch->update($request->validated());

    //     return new BranchResource($branch);
    // }

    public function destroy($id)
    {
        // $branch->delete();
        CashierExpenses::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
