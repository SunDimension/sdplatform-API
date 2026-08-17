<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentsStoreRequest;
use App\Http\Requests\PaymentsUpdateRequest;
use App\Http\Resources\PaymentCollection;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentsController extends Controller
{
    public function index(Request $request): PaymentCollection
    {
        $payments = Payment::all();

        return new PaymentCollection($payments);
    }

    public function store(PaymentsStoreRequest $request): PaymentResource
    {
        $payment = Payment::create($request->validated());

        return new PaymentResource($payment);
    }

    public function show(Request $request, Payment $payment): PaymentResource
    {
        return new PaymentResource($payment);
    }

    public function update(PaymentsUpdateRequest $request, Payment $payment): PaymentResource
    {
        $payment->update($request->validated());

        return new PaymentResource($payment);
    }

    public function destroy(Request $request, Payment $payment): Response
    {
        $payment->delete();

        return response()->noContent();
    }
}
