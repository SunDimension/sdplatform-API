<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentTypeStoreRequest;
use App\Http\Requests\PaymentTypeUpdateRequest;
use App\Http\Resources\PaymentTypeCollection;
use App\Http\Resources\PaymentTypeResource;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentTypeController extends Controller
{
    public function index(Request $request): PaymentTypeCollection
    {
        $paymentTypes = PaymentType::all();

        return new PaymentTypeCollection($paymentTypes);
    }

    public function store(PaymentTypeStoreRequest $request): PaymentTypeResource
    {
        $paymentType = PaymentType::create($request->validated());

        return new PaymentTypeResource($paymentType);
    }

    public function show(Request $request, PaymentType $paymentType): PaymentTypeResource
    {
        return new PaymentTypeResource($paymentType);
    }

    public function update(PaymentTypeUpdateRequest $request, PaymentType $paymentType): PaymentTypeResource
    {
        $paymentType->update($request->validated());

        return new PaymentTypeResource($paymentType);
    }
public function destroy($id)
    {   
       
        PaymentType::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
