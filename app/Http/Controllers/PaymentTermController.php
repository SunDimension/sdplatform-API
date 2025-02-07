<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentTermStoreRequest;
use App\Http\Requests\PaymentTermUpdateRequest;
use App\Http\Resources\PaymentTermCollection;
use App\Http\Resources\PaymentTermResource;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentTermController extends Controller
{
    public function index(Request $request): PaymentTermCollection
    {
        $paymentTerms = PaymentTerm::all();

        return new PaymentTermCollection($paymentTerms);
    }

    public function store(PaymentTermStoreRequest $request): PaymentTermResource
    {
        $paymentTerm = PaymentTerm::create($request->validated());

        return new PaymentTermResource($paymentTerm);
    }

    public function show(Request $request, PaymentTerm $paymentTerm): PaymentTermResource
    {
        return new PaymentTermResource($paymentTerm);
    }

    public function update(PaymentTermUpdateRequest $request, PaymentTerm $paymentTerm): PaymentTermResource
    {
        $paymentTerm->update($request->validated());

        return new PaymentTermResource($paymentTerm);
    }

   public function destroy($id)
    {   
       
        PaymentTerm::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
