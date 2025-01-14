<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashierExpenseStoreRequest;
// use App\Http\Requests\BranchUpdateRequest;
use App\Http\Resources\CashierExpenseCollection;
use App\Http\Resources\CashierExpenseResource;
use App\Models\CashierExpense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CashierExpenseController extends Controller
{
    public function index(Request $request): CashierExpenseCollection
    {
        $CashierExpense = CashierExpense::all();

        return new CashierExpenseCollection($CashierExpense);
    }

    public function pending(Request $request): CashierExpenseCollection
    {
        $CashierExpense = CashierExpense::where('status','pending')->where('branch_id',auth()->user()->branch_id)->get();

        return new CashierExpenseCollection($CashierExpense);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id'=>['required']
        ]);

        
        $receiveOrder = CashierExpense::findOrFail($validated['id']);
        $receiveOrder->approval_comment = $validated['comment'];
        $receiveOrder->status = $validated['status'];
        $receiveOrder->approved_by = auth()->user()->id;
        $receiveOrder->approval_date = now();
        $receiveOrder->save();

        
        return new CashierExpenseResource($receiveOrder);
    }

    public function store(CashierExpenseStoreRequest $request): CashierExpenseResource
    {
        $CashierExpense = CashierExpense::create($request->validated());

        return new CashierExpenseResource($CashierExpense);
    }

    public function show(Request $request, CashierExpense $CashierExpense): CashierExpenseResource
    {
        return new CashierExpenseResource($CashierExpense);
    }

    // public function update(BranchUpdateRequest $request, Branch $branch): BranchResource
    // {
    //     $branch->update($request->validated());

    //     return new BranchResource($branch);
    // }

    public function destroy($id)
    {
        // $branch->delete();
        CashierExpense::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
