<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferOrderApproveRequest;
use App\Http\Requests\StoreTransferOrderStoreRequest;
use App\Http\Requests\StoreTransferOrderUpdateRequest;
use App\Http\Resources\StoreTransferOrderCollection;
use App\Http\Resources\StoreTransferOrderResource;
use App\Models\StoreTransferItem;
use App\Models\StoreTransferOrder;
use App\Services\AccountingEntryService;
use App\Services\StoreTransferApprovalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class StoreTransferOrderController extends Controller
{
    protected $accountingService;
    protected $approvalService;

    public function __construct(
        AccountingEntryService $accountingService,
        StoreTransferApprovalService $approvalService
    ) {
        $this->accountingService = $accountingService;
        $this->approvalService = $approvalService;
    }

    public function index(Request $request)
    {
        $storeTransferOrders = StoreTransferOrder::all();

        return new StoreTransferOrderCollection($storeTransferOrders);
    }

    public function pending(Request $request)
    {
        $receiveOrders = StoreTransferOrder::where('source_status','outgoing')->where('source_store_id',auth()->user()->store_id)->get();
        $receiveOrders2 = StoreTransferOrder::where('destination_status','incoming')->where('source_status','approved')->where('destination_store_id',auth()->user()->store_id)->get();
        // $receiveOrders = ReceiveOrder::where('status','Pending')->get();
        return response()->json(['data' => [
            'incoming' =>  StoreTransferOrderResource::collection($receiveOrders),
            'outgoing' => StoreTransferOrderResource::collection($receiveOrders2)
        ]]);
    }

    public function branch_pending(Request $request)
    {
        $receiveOrders = StoreTransferOrder::where('source_status','Pending')->where('source_branch_id',auth()->user()->branch_id)->get();
        $receiveOrders2 = StoreTransferOrder::where('destination_status','Pending')->where('destination_branch_id',auth()->user()->branch_id)->get();
        // $receiveOrders = ReceiveOrder::where('status','Pending')->get();
        // return new StoreTransferOrderCollection($receiveOrders);
        return response()->json(['data' => [
            'incoming' =>  StoreTransferOrderResource::collection($receiveOrders),
            'outgoing' => StoreTransferOrderResource::collection($receiveOrders2)
        ]]);
    }

    public function store(StoreTransferOrderStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['source_status'] ='outgoing';
        $validated['destination_status'] = 'incoming';   
        $validated['created_by'] = auth()->user()->id;
        // $validated['transfer_date'] = now();
        $storeTransferOrder = StoreTransferOrder::create($validated);

        foreach ($validated['items'] as $item) {
            StoreTransferItem::create([
                'transfer_order_id' => $storeTransferOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'quantity_pieces' => $item['quantity_pieces'],
                'unit_price' => $item['unit_price'],
                'description' => $item['description'],
                'created_by' => auth()->user()->id
            ]);
        }

        return new StoreTransferOrderResource($storeTransferOrder);
    }

    public function approve(StoreTransferOrderApproveRequest $request)
    {
        $validated = $request->validated();
        
        try {
            DB::beginTransaction();
            
            $storeTransferOrder = $this->approvalService->findTransferOrder($validated['id'], $validated['source']);
            
            if (!$storeTransferOrder) {
                throw ValidationException::withMessages([
                    'id' => 'Transfer order not found or not in correct status for approval.'
                ]);
            }

            $this->approvalService->processApproval($storeTransferOrder, $validated);
            
            // Create accounting entries if transfer is being approved
            if ($this->approvalService->shouldCreateAccountingEntries($storeTransferOrder, $validated)) {
                $this->approvalService->createAccountingEntries($storeTransferOrder);
            }
            
            DB::commit();
            
            Log::info('Store Transfer Order approved successfully', [
                'order_id' => $storeTransferOrder->id,
                'order_number' => $storeTransferOrder->order_number,
                'approved_by' => auth()->id(),
                'status' => $validated['status']
            ]);
            
            return new StoreTransferOrderResource($storeTransferOrder);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Transfer Order approval failed', [
                'order_id' => $validated['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    public function show(Request $request, StoreTransferOrder $storeTransferOrder)
    {
        return new StoreTransferOrderResource($storeTransferOrder->load('items'));
    }

    public function update(StoreTransferOrderUpdateRequest $request, StoreTransferOrder $storeTransferOrder)
    {
        $storeTransferOrder->update($request->validated());

        return new StoreTransferOrderResource($storeTransferOrder);
    }

    public function destroy(Request $request, StoreTransferOrder $storeTransferOrder)
    {
        $storeTransferOrder->delete();

        return response()->noContent();
    }




}
