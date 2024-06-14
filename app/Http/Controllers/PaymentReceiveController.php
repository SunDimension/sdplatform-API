<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentReceiveStoreRequest;
use App\Http\Requests\PaymentReceiveUpdateRequest;
use App\Http\Resources\PaymentReceifeCollection;
use App\Http\Resources\PaymentReceiveResource;
use App\PaymentReceive;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentReceiveController extends Controller
{
    public function index(Request $request): PaymentReceifeCollection
    {
        $paymentReceives = PaymentReceive::all();

        return new PaymentReceifeCollection($paymentReceives);
    }

    public function store(PaymentReceiveStoreRequest $request): PaymentReceiveResource
    {
        $paymentReceive = PaymentReceive::create($request->validated());

        return new PaymentReceiveResource($paymentReceive);
    }

    public function show(Request $request, PaymentReceive $paymentReceive): PaymentReceiveResource
    {
        return new PaymentReceiveResource($paymentReceive);
    }

    public function update(PaymentReceiveUpdateRequest $request, PaymentReceive $paymentReceive): PaymentReceiveResource
    {
        $paymentReceive->update($request->validated());

        return new PaymentReceiveResource($paymentReceive);
    }

    public function destroy(Request $request, PaymentReceive $paymentReceive): Response
    {
        $paymentReceive->delete();

        return response()->noContent();
    }
}
