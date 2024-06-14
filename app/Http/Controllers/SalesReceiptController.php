<?php

namespace App\Http\Controllers;

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
        $salesReceipts = SalesReceipt::all();

        return new SalesReceiptCollection($salesReceipts);
    }

    public function store(SalesReceiptStoreRequest $request): SalesReceiptResource
    {
        $salesReceipt = SalesReceipt::create($request->validated());

        return new SalesReceiptResource($salesReceipt);
    }

    public function show(Request $request, SalesReceipt $salesReceipt): SalesReceiptResource
    {
        return new SalesReceiptResource($salesReceipt);
    }

    public function update(SalesReceiptUpdateRequest $request, SalesReceipt $salesReceipt): SalesReceiptResource
    {
        $salesReceipt->update($request->validated());

        return new SalesReceiptResource($salesReceipt);
    }

    public function destroy(Request $request, SalesReceipt $salesReceipt): Response
    {
        $salesReceipt->delete();

        return response()->noContent();
    }
}
