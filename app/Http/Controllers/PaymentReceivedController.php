<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentReceivedStoreRequest;
use App\Http\Requests\PaymentReceivedUpdateRequest;
use App\Http\Resources\PaymentReceivedCollection;
use App\Http\Resources\PaymentReceivedResource;
use App\Models\PaymentReceived;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentReceivedController extends Controller
{
    public function index(Request $request): PaymentReceivedCollection
    {
        $paymentReceiveds = PaymentReceived::all();

        return new PaymentReceivedCollection($paymentReceiveds);
    }

    public function store(PaymentReceivedStoreRequest $request): PaymentReceivedResource
    {
        $paymentReceived = PaymentReceived::create($request->validated());

        return new PaymentReceivedResource($paymentReceived);
    }

    public function show(Request $request, PaymentReceived $paymentReceived): PaymentReceivedResource
    {
        return new PaymentReceivedResource($paymentReceived);
    }

    public function update(PaymentReceivedUpdateRequest $request, PaymentReceived $paymentReceived): PaymentReceivedResource
    {
        $paymentReceived->update($request->validated());

        return new PaymentReceivedResource($paymentReceived);
    }

    public function destroy(Request $request, PaymentReceived $paymentReceived): Response
    {
        $paymentReceived->delete();

        return response()->noContent();
    }
}
