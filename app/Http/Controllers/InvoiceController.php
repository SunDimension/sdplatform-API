<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): InvoiceCollection
    {
        $invoices = Invoice::all();

        return new InvoiceCollection($invoices);
    }

    public function store(InvoiceStoreRequest $request): InvoiceResource
    {
        $invoice = Invoice::create($request->validated());

        return new InvoiceResource($invoice);
    }

    public function show(Request $request, Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice);
    }

    public function update(InvoiceUpdateRequest $request, Invoice $invoice): InvoiceResource
    {
        $invoice->update($request->validated());

        return new InvoiceResource($invoice);
    }

        
    public function destroy($id): Response
    {   
       
        Invoice::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
