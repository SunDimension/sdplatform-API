<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentModeStoreRequest;
use App\Http\Requests\PaymentModeUpdateRequest;
use App\Http\Resources\PaymentModeCollection;
use App\Http\Resources\PaymentModeResource;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentModeController extends Controller
{
    public function index(Request $request): PaymentModeCollection
    {
        $paymentModes = PaymentMode::all();

        return new PaymentModeCollection($paymentModes);
    }

    public function store(PaymentModeStoreRequest $request): PaymentModeResource
    {
        $paymentMode = PaymentMode::create($request->validated());

        return new PaymentModeResource($paymentMode);
    }

    public function show(Request $request, PaymentMode $paymentMode): PaymentModeResource
    {
        return new PaymentModeResource($paymentMode);
    }

    public function update(PaymentModeUpdateRequest $request, PaymentMode $paymentMode): PaymentModeResource
    {
        $paymentMode->update($request->validated());

        return new PaymentModeResource($paymentMode);
    }

  public function destroy($id)
    {   
       
        PaymentMode::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
