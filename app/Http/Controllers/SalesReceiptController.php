<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesReceiptStoreRequest;
use App\Http\Requests\SalesReceiptUpdateRequest;
use App\Http\Resources\SalesReceiptCollection;
use App\Http\Resources\SalesReceiptResource;
use App\Models\SalesOrder;
use App\Models\SalesReceipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SalesReceiptController extends Controller
{
    public function index(Request $request)
    {
        $salesreceipt = SalesReceipt::all();
        return new SalesReceiptCollection($salesreceipt);
    }

    public function getbynumber($orderno)
    {
        $salesOrders = SalesReceipt::with('salesorder')->where('sales_receipt_number', $orderno)->first();
        Log::debug($salesOrders);
        return response()->json(['data' => new SalesReceiptResource($salesOrders)]);
    }

    public function pendingRelease()
    {
        //$salesOrders = SalesReceipt::with('salesorder');
        $user = Auth::user();
        // Log::debug($user);

        $salesReceipts = SalesReceipt::with('salesOrder')->whereHas('salesOrder.itemsold', function ($query) use ($user) {
            // Add your specific criteria for ItemSold here
            $query->where('store_id', $user->store_id); // Example condition
        })->get();
        //Log::debug($salesOrders);
        return response()->json(['data' => SalesReceiptResource::collection($salesReceipts)]);
    }

    public function pendingReleaseOrder($orderno)
    {
        //$salesOrders = SalesReceipt::with('salesorder');
        $user = Auth::user();
        //Log::debug($user);

        $salesReceipts = SalesReceipt::where("sales_receipt_number", $orderno)->whereHas('salesOrder.itemSold', function ($query) use ($user) {
            // Add your specific criteria for ItemSold here
            $query->where('store_id', $user->store_id); // Example condition
        })->with(['salesOrder', 'salesOrder.itemSold' => function ($query) use ($user){
            // Only retrieve specific fields from ItemSold
            $query->where('store_id', $user->store_id);
        }])
            // ->select('id', 'sales_order_id', 'receipt_number') // Select specific fields from SalesReceipt
            ->first();
        //Log::debug($salesReceipts);
        return response()->json(['data' => new SalesReceiptResource($salesReceipts)]);
    }

    public function store(SalesReceiptStoreRequest $request)
    {
        $data = $request->validated();
        $salesreceipt = SalesReceipt::create($data);
        $order = SalesOrder::where('id', $salesreceipt->sales_order_id)->first();
        $order->status = 'Paid';
        $order->save();

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
