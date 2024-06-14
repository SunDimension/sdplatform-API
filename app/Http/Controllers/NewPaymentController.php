<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPaymentStoreRequest;
use App\Http\Requests\NewPaymentUpdateRequest;
use App\Http\Resources\NewPaymentCollection;
use App\Http\Resources\NewPaymentResource;
use App\Models\NewPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NewPaymentController extends Controller
{
    public function index(Request $request): NewPaymentCollection
    {
        $newPayments = NewPayment::all();

        return new NewPaymentCollection($newPayments);
    }

    public function store(NewPaymentStoreRequest $request): NewPaymentResource
    {
        $newPayment = NewPayment::create($request->validated());

        return new NewPaymentResource($newPayment);
    }

    public function show(Request $request, NewPayment $newPayment): NewPaymentResource
    {
        return new NewPaymentResource($newPayment);
    }

    public function update(NewPaymentUpdateRequest $request, NewPayment $newPayment): NewPaymentResource
    {
        $newPayment->update($request->validated());

        return new NewPaymentResource($newPayment);
    }

    public function destroy(Request $request, NewPayment $newPayment): Response
    {
        $newPayment->delete();

        return response()->noContent();
    }
}
