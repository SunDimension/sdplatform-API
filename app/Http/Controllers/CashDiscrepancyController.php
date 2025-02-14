<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashDiscrepancyStoreRequest;
use App\Http\Requests\CashDiscrepancyUpdateRequest;
use App\Http\Resources\CashDiscrepancyCollection;
use App\Http\Resources\CashDiscrepancyResource;
use App\Models\CashDiscrepancy;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CashDiscrepancyController extends Controller
{
    public function index(Request $request): CashDiscrepancyCollection
    {
        $cashDs = CashDiscrepancy::all();

        return new CashDiscrepancyCollection($cashDs);
    }

    public function pending(Request $request): CashDiscrepancyCollection
    {
        $cashierExpenses = CashDiscrepancy::where('status','pending')->where('branch_id',auth()->user()->branch_id)->get();

        return new CashDiscrepancyCollection($cashierExpenses);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id'=>['required']
        ]);
        $receiveOrder = CashDiscrepancy::findOrFail($validated['id']);
        $receiveOrder->approval_comment = $validated['comment'];
        $receiveOrder->status = $validated['status'];
        $receiveOrder->approved_by = auth()->user()->id;
        $receiveOrder->approval_date = now();
        $receiveOrder->save();

        return new CashDiscrepancyCollection($receiveOrder);
    }

    public function store(CashDiscrepancyStoreRequest $request): CashDiscrepancyResource
    {
        $cashDs = CashDiscrepancy::create($request->validated());

        return new CashDiscrepancyResource($cashDs);
    }

    public function show(Request $request, CashDiscrepancy $cashDs): CashDiscrepancyResource
    {
        return new CashDiscrepancyResource($cashDs);
    }

    public function update(CashDiscrepancyUpdateRequest $request, CashDiscrepancy $cashDs):CashDiscrepancyResource
    {
        $cashDs->update($request->validated());

        return new CashDiscrepancyResource($cashDs);
    }

    public function destroy($id)
    {
       CashDiscrepancy::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
