<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentVoucherDetailStoreRequest;
use App\Http\Requests\PaymentVoucherDetailUpdateRequest;
use App\Http\Resources\PaymentVoucherDetailCollection;
use App\Http\Resources\PaymentVoucherDetailResource;
use App\Models\PaymentVoucherDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentVoucherDetailController extends Controller
{
    public function index(Request $request): PaymentVoucherDetailCollection
    {
        $paymentVoucherDetails = PaymentVoucherDetail::all();

        return new PaymentVoucherDetailCollection($paymentVoucherDetails);
    }

    public function store(PaymentVoucherDetailStoreRequest $request): PaymentVoucherDetailResource
    {
        $paymentVoucherDetail = PaymentVoucherDetail::create($request->validated());

        return new PaymentVoucherDetailResource($paymentVoucherDetail);
    }

    public function show(Request $request, PaymentVoucherDetail $paymentVoucherDetail): PaymentVoucherDetailResource
    {
        return new PaymentVoucherDetailResource($paymentVoucherDetail);
    }

    public function update(PaymentVoucherDetailUpdateRequest $request, PaymentVoucherDetail $paymentVoucherDetail): PaymentVoucherDetailResource
    {
        $paymentVoucherDetail->update($request->validated());

        return new PaymentVoucherDetailResource($paymentVoucherDetail);
    }

    public function destroy(Request $request, PaymentVoucherDetail $paymentVoucherDetail): Response
    {
        $paymentVoucherDetail->delete();

        return response()->noContent();
    }
}
