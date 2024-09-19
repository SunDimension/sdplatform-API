<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentVoucherStoreRequest;
use App\Http\Requests\PaymentVoucherUpdateRequest;
use App\Http\Resources\PaymentVoucherCollection;
use App\Http\Resources\PaymentVoucherResource;
use App\Models\PaymentVoucher;
use App\Models\PaymentVoucherDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentVoucherController extends Controller
{
    public function index(Request $request): PaymentVoucherCollection
    {
        $paymentVouchers = PaymentVoucher::all();

        return new PaymentVoucherCollection($paymentVouchers);
    }

    public function store(PaymentVoucherStoreRequest $request): PaymentVoucherResource
    {
        //$paymentVoucher = PaymentVoucher::create($request->validated());
        $data = $request->validated();
        $paymentVoucher = PaymentVoucher::create($data);
        //Log::debug($data);
        foreach ($data['voucher_entries'] as $clauseEntry) {
            PaymentVoucherDetail::create([
                'payment_voucher_id'=>$paymentVoucher->id,
                'item_id' => $clauseEntry['item_id'],
                'amount' => $clauseEntry['amount'],
                // 'expense_account_id' => $clauseEntry['expense_account_id'],
                'quantity' => $clauseEntry['quantity'],
                'description' => $clauseEntry['description'],

                 //'amount' => $journalEntry->id,
            ]);

            
        }

        return new PaymentVoucherResource($paymentVoucher);
    }

    public function show(Request $request, PaymentVoucher $paymentVoucher): PaymentVoucherResource
    {
        return new PaymentVoucherResource($paymentVoucher);
    }

    public function update(PaymentVoucherUpdateRequest $request, PaymentVoucher $paymentVoucher): PaymentVoucherResource
    {
        $paymentVoucher->update($request->validated());

        return new PaymentVoucherResource($paymentVoucher);
    }

   public function destroy($id)
    {   
       
        PaymentVoucher::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
