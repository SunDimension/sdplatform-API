<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesInvoiceStoreRequest;
use App\Http\Requests\SalesInvoiceUpdateRequest;
use App\Http\Resources\SalesInvoiceCollection;
use App\Http\Resources\SalesInvoiceResource;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesInvoiceController extends Controller
{
    public function index(Request $request): SalesInvoiceCollection
    {
        $salesinvoice = SalesInvoice::all();

        return new SalesInvoiceCollection($salesinvoice);
    }
    public function store(SalesInvoiceStoreRequest $request): SalesInvoiceResource
    {
        $salesinvoice = SalesInvoice::create($request->validated());

        return new SalesInvoiceResource($salesinvoice);
    }

    public function show(Request $request, SalesInvoice $salesinvoice): SalesInvoiceResource
    {
        return new SalesInvoiceResource($salesinvoice);
    }

    public function update(SalesInvoiceUpdateRequest $request, SalesInvoice $salesinvoice): SalesInvoiceResource
    {
        $salesinvoice->update($request->validated());

        return new SalesInvoiceResource($salesinvoice);
    }

   public function destroy($id)
    {   
       
        SalesInvoice::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
