<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditTransactionStoreRequest;
use App\Http\Requests\CreditTransactionUpdateRequest;
use App\Http\Resources\CreditTransactionCollection;
use App\Http\Resources\CreditTransactionResource;
use App\Models\CreditTransaction;
use App\Models\Customer;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreditTransactionController extends Controller
{
    public function index(Request $request)
    {
        $creditTransactions = CreditTransaction::all();

        return new CreditTransactionCollection($creditTransactions);
    }

    public function store(CreditTransactionStoreRequest $request)
    {
        $data = $request->validated();
        $data["created_by"] = auth()->user()->id;
        $creditTransaction = CreditTransaction::create($data);
        if($creditTransaction->type == 'credit')
        {
            $salesOrder = SalesOrder::findOrFail($creditTransaction->sales_order_id);
            $salesOrder->credit_limit = $creditTransaction->credit_balance_before;
            $salesOrder->credit_balance = $creditTransaction->amount;
            $salesOrder->status = "Pending";

            if($salesOrder->total_amount == $creditTransaction->amount)
                $salesOrder->status = "Approved";
            $salesOrder->save();

            $customer = Customer::findOrFail($creditTransaction->customer_id);
            $customer->credit_balance = $customer->credit_balance??$customer->credit_limit - $creditTransaction->amount;
            $customer->save(); 
        }
        elseif($creditTransaction->type == 'payment')
        {
            $creditTransaction->type == 'credit';

            $customer = Customer::findOrFail($creditTransaction->customer_id);
            $customer->credit_balance = $customer->credit_balance + $creditTransaction->amount;
            $customer->save(); 
        }




        return new CreditTransactionResource($creditTransaction);
    }

    public function show(Request $request, CreditTransaction $creditTransaction)
    {
        return new CreditTransactionResource($creditTransaction);
    }

    public function update(CreditTransactionUpdateRequest $request, CreditTransaction $creditTransaction)
    {
        $creditTransaction->update($request->validated());

        return new CreditTransactionResource($creditTransaction);
    }

    public function destroy(Request $request, CreditTransaction $creditTransaction)
    {
        $creditTransaction->delete();

        return response()->noContent();
    }
}
