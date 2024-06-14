<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReceivedDetailStoreRequest;
use App\Http\Requests\PurchaseReceivedDetailUpdateRequest;
use App\Http\Resources\PurchaseReceivedDetailCollection;
use App\Http\Resources\PurchaseReceivedDetailResource;
use App\Models\PurchaseReceivedDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PurchaseReceivedDetailController extends Controller
{
    public function index(Request $request): PurchaseReceivedDetailCollection
    {
        $purchaseReceivedDetails = PurchaseReceivedDetail::all();

        return new PurchaseReceivedDetailCollection($purchaseReceivedDetails);
    }

    public function store(PurchaseReceivedDetailStoreRequest $request): PurchaseReceivedDetailResource
    {
        $purchaseReceivedDetail = PurchaseReceivedDetail::create($request->validated());

        return new PurchaseReceivedDetailResource($purchaseReceivedDetail);
    }

    public function show(Request $request, PurchaseReceivedDetail $purchaseReceivedDetail): PurchaseReceivedDetailResource
    {
        return new PurchaseReceivedDetailResource($purchaseReceivedDetail);
    }

    public function update(PurchaseReceivedDetailUpdateRequest $request, PurchaseReceivedDetail $purchaseReceivedDetail): PurchaseReceivedDetailResource
    {
        $purchaseReceivedDetail->update($request->validated());

        return new PurchaseReceivedDetailResource($purchaseReceivedDetail);
    }

    public function destroy(Request $request, PurchaseReceivedDetail $purchaseReceivedDetail): Response
    {
        $purchaseReceivedDetail->delete();

        return response()->noContent();
    }
}
