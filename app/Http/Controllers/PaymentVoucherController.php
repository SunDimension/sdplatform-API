<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentVoucherStoreRequest;
use App\Http\Requests\PaymentVoucherUpdateRequest;
use App\Http\Resources\PaymentVoucherCollection;
use App\Http\Resources\PaymentVoucherResource;
use App\Models\PaymentVoucher;
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
        $paymentVoucher = PaymentVoucher::create($request->validated());

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
