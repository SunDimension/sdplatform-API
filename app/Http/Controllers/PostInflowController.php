<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostInflowStoreRequest;
use App\Http\Requests\PostInflowUpdateRequest;
use App\Http\Resources\PostInflowCollection;
use App\Http\Resources\PostInflowResource;
use App\Models\PostInflow;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log as FacadesLog;
use App\Models\SalesReceipt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PostInflowController extends Controller
{

    public function index(Request $request): PostInflowCollection
    {
        // Validate incoming request parameters
        $validated = $request->validate([
            'bank_id' => 'nullable',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'inflow_status' => 'nullable'
        ]);

        // Log the validated input for debugging
        FacadesLog::debug($validated);

        // Assign variables, default to null if not present
        $bankId = $validated['bank_id'] ?? null;
        $inflowStatus = $validated['inflow_status'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        // Start building the query for inflows
        $query = PostInflow::query();

        // Filter by bank_id if provided
        if ($bankId) {
            $query->where('bank_id', $bankId);
        }

        // Filter by inflow_status if provided
        if ($inflowStatus) {
            $query->where('inflow_status', $inflowStatus);
        }

        // Filter by date range if both fromDate and toDate are provided
        if ($fromDate && $toDate) {
            $query->whereBetween('inflow_date', [Carbon::parse($fromDate), Carbon::parse($toDate)]);
        } elseif ($fromDate) {
            // Filter for inflows from the fromDate onwards
            $query->where('inflow_date', '>=', Carbon::parse($fromDate));
        } elseif ($toDate) {
            // Filter for inflows up to the toDate
            $query->where('inflow_date', '<=', Carbon::parse($toDate));
        }

        // Fetch the filtered inflows with pagination (15 per page as an example)
        $postInflows = $query->paginate(15);

        // Return the inflows as a resource collection
        return new PostInflowCollection($postInflows);
    }
    public function store(PostInflowStoreRequest $request): PostInflowResource
    {
        $data = $request->validated();

        // Set status based on whether customer_id is provided
        $data['inflow_status'] = isset($data['customer_id']) ? 3 : 6;

        $postinflow = PostInflow::create($data);

        return new PostInflowResource($postinflow);
    }

    public function show(Request $request, PostInflow $postinflow): PostInflowResource
    {
        return new PostInflowResource($postinflow);
    }

    public function update(PostInflowUpdateRequest $request, $post_inflow): PostInflowResource
    {
        $postinflow = PostInflow::findOrFail($post_inflow);
        $data = $request->validated();

        // Update status if customer is assigned and current status is unclaimed
        if (isset($data['customer_id']) && $postinflow->inflow_status == 6) {
            $data['inflow_status'] = 3;
        }

        $postinflow->update($data);

        return new PostInflowResource($postinflow);
    }

    public function destroy($id)
    {

        PostInflow::destroy($id);


        return response(null, Response::HTTP_NO_CONTENT);
    }

    // Add this method to your PostInflowController
    public function adjustForPurchases(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'customer_id' => 'required|integer|exists:customers,id',
                'sales_receipt_id' => 'nullable|integer|exists:sales_receipts,id',
            ]);

            $customerId = $validated['customer_id'];
            $salesReceiptId = $validated['sales_receipt_id'] ?? null;

            // Get all deposit payments for this customer
            $query = SalesReceipt::where('customer_id', $customerId)
                ->whereJsonContains('payment_detail', ['payment_type' => 'Deposit']);

            if ($salesReceiptId) {
                $query->where('id', $salesReceiptId);
            }

            $receipts = $query->get();

            foreach ($receipts as $receipt) {
                $payments = is_string($receipt->payment_detail)
                    ? json_decode($receipt->payment_detail, true)
                    : $receipt->payment_detail;

                foreach ($payments as $payment) {
                    if ($payment['payment_type'] === 'Deposit') {
                        $amountToDeduct = $payment['amount'];

                        $inflows = PostInflow::where('customer_id', $customerId)
                            ->where('inflow_status', 3)
                            ->where('amount', '>', 0)
                            ->orderBy('inflow_date', 'asc')
                            ->lockForUpdate()
                            ->get();

                        foreach ($inflows as $inflow) {
                            if ($amountToDeduct <= 0) break;

                            $deductible = min($inflow->amount_available, $amountToDeduct);

                            $inflow->amount -= $deductible;
                            $inflow->amount_used += $deductible;

                            if ($inflow->amount_available <= 0) {
                                $inflow->inflow_status = 12; // Fully Used
                            }

                            $inflow->save();
                            $amountToDeduct -= $deductible;
                        }

                        if ($amountToDeduct > 0) {
                            Log::warning("Insufficient deposit for receipt {$receipt->id}, remaining: $amountToDeduct");
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Post inflows adjusted successfully',
                'customer_id' => $customerId,
                'adjusted_receipts' => $receipts->pluck('id'),
                'adjusted_amount' => $receipts->sum(function ($r) {
                    $payments = is_string($r->payment_detail)
                        ? json_decode($r->payment_detail, true)
                        : $r->payment_detail;
                    return collect($payments)
                        ->where('payment_type', 'Deposit')
                        ->sum('amount');
                })
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Adjustment failed: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to adjust post inflows',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
