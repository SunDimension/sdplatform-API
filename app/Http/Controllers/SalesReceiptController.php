<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesReceiptStoreRequest;
use App\Http\Requests\SalesReceiptUpdateRequest;
use App\Http\Resources\SalesReceiptCollection;
use App\Http\Resources\SalesReceiptResource;
use App\Models\SalesReceipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesReceiptController extends Controller
{
    public function index(Request $request): SalesReceiptCollection
    {
        $salesreceipt = SalesReceipt::all();

        return new SalesReceiptCollection($salesreceipt);
    }
    public function store(SalesReceiptStoreRequest $request): SalesReceiptResource
    {
        $salesreceipt = SalesReceipt::create($request->validated());

        return new SalesReceiptResource($salesreceipt);
    }

    public function show(Request $request, SalesReceipt $salesreceipt): SalesReceiptResource
    {
        return new SalesReceiptResource($salesreceipt);
    }

    public function update(SalesReceiptUpdateRequest $request, SalesReceipt $salesreceipt): SalesReceiptResource
    {
        $salesreceipt->update($request->validated());

        return new SalesReceiptResource($salesreceipt);
    }

   public function destroy($id)
    {   
       
        SalesReceipt::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
