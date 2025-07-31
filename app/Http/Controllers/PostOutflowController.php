<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostOutflowStoreRequest;
use App\Http\Requests\PostOutflowUpdateRequest;
use App\Http\Resources\PostOutflowCollection;
use App\Http\Resources\PostOutflowResource;
use App\Models\PostOutflow;
use App\Models\PostInflow;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AccountingEntryService;
use App\Models\TransactionJournalEntry;

class PostOutflowController extends Controller
{
    protected $accountingEntryService;

    public function __construct(AccountingEntryService $accountingEntryService)
    {
        $this->accountingEntryService = $accountingEntryService;
    }
  
  public function index(Request $request): PostOutflowCollection
{
    // Get query parameters from the request
    
    $validated = $request->validate([
        'bene_bank' => 'nullable',
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date',
    ]);

    FacadesLog::debug($validated);

    $beneBank = $validated['bene_bank'];
    $fromDate = $validated['from_date'];
    $toDate = $validated['to_date'];

    // Start building the query
    $query = PostOutflow::query();

    // Filter by beneficiary bank if provided
    if ($beneBank) {
        $query->where('bene_bank', $beneBank);
    }

    // Filter by date range (fromDate to toDate) if both dates are provided
    if ($fromDate && $toDate) {
        $query->whereBetween('outflow_date', [Carbon::parse($fromDate), Carbon::parse($toDate)]);
    } elseif ($fromDate) {
        // If only fromDate is provided, filter for outflows from that date onwards
        $query->where('outflow_date', '>=', Carbon::parse($fromDate));
    } elseif ($toDate) {
        // If only toDate is provided, filter for outflows up to that date
        $query->where('outflow_date', '<=', Carbon::parse($toDate));
    }
    
    // Get the filtered outflows
    $postoutflows = $query->get();

    // Return as a resource collection
    return new PostOutflowCollection($postoutflows);
}

//  public function index(Request $request): PostOutflowCollection
//     {
//         $postoutflow = PostOutflow::all();

//         return new PostOutflowCollection($postoutflow);
//     }








public function store(PostOutflowStoreRequest $request): PostOutflowResource
{
    // Use a database transaction to ensure data consistency
    DB::beginTransaction();

    try {
        // Fetch the latest post_inflows record for the given customer_id
        $postInflow = DB::table('post_inflows')
            ->where('customer_id', $request->customer_id)
            ->orderBy('created_at', 'desc') // Get the latest transaction first
            ->first();

        // Check if a post_inflows record exists
        if (!$postInflow) {
            throw new \Exception('No inflow details found for the given customer ID.');
        }

        // Ensure the post_inflows amount is sufficient
        if ($postInflow->amount < $request->amount) {
            throw new \Exception('Insufficient amount in post_inflows.');
        }

        // Subtract the submitted amount from the post_inflows amount
        DB::table('post_inflows')
            ->where('id', $postInflow->id)
            ->update(['amount' => $postInflow->amount - $request->amount]);

        // Create the post_outflows record
        $postOutflow = PostOutflow::create($request->validated());

        // Generate accounting entries for the post outflow
        try {
            $this->accountingEntryService->generatePostOutflowEntries($postOutflow);
        } catch (\Exception $e) {
            Log::error("Failed to generate accounting entries for post outflow: " . $e->getMessage());
            // Don't throw here, as the post outflow was created successfully
            // Just log the error for debugging
        }

        // Commit the transaction
        DB::commit();

        return new PostOutflowResource($postOutflow);
    } catch (\Exception $e) {
        // Rollback the transaction in case of an error
        DB::rollBack();

        // Create a new PostOutflow instance with an error message
        $errorPostOutflow = new PostOutflow([
            'error' => true,
            'message' => $e->getMessage(),
        ]);

        // Return the error as a PostOutflowResource
        return new PostOutflowResource($errorPostOutflow);
    }
}


    public function show(Request $request, PostOutflow $postoutflow): PostOutflowResource
    {
        return new PostOutflowResource($postoutflow);
    }

    public function update(PostOutflowUpdateRequest $request, PostOutflow $postoutflow): PostOutflowResource
    {
        $postoutflow->update($request->validated());

        return new PostOutflowResource($postoutflow);
    }

   public function destroy($id)
    {   
       
        PostOutflow::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }


public function getCustomerInflowDetails(Request $request)
{
    // Validate the request
    $validated = $request->validate([
        'customer_id' => 'required|integer|exists:post_inflows,customer_id',
    ]);

    // Fetch all inflow records for the given customer
    $inflowDetails = DB::table('post_inflows')
        ->where('customer_id', $validated['customer_id'])
        ->select('amount', 'bank_id')
        ->orderBy('created_at', 'desc') // Get the latest transaction first
        ->get();

    // Check if inflow details were found
    if ($inflowDetails->isEmpty()) {
        return response()->json([
            'message' => 'No inflow details found for the given customer ID.',
            'data' => []
        ], 200);
    }

    // Return inflow details
    return response()->json([
        'data' => $inflowDetails
    ]);
}

/**
 * Generate accounting entries for a specific post outflow
 */
public function generateAccountingEntries($id)
{
    $postOutflow = PostOutflow::findOrFail($id);

    // Check if accounting entries already exist for this post outflow
    $existingEntries = TransactionJournalEntry::where('description', 'LIKE', "%Post Outflow #{$postOutflow->id}%")->first();
    
    if ($existingEntries) {
        return response()->json([
            'message' => 'Accounting entries already exist for this post outflow',
            'post_outflow_id' => $id
        ], 400);
    }

    try {
        $journalEntry = $this->accountingEntryService->generatePostOutflowEntries($postOutflow);
        
        return response()->json([
            'message' => 'Accounting entries generated successfully',
            'post_outflow_id' => $id,
            'journal_entry_id' => $journalEntry->id
        ]);
    } catch (\Exception $e) {
        Log::error("Failed to generate accounting entries for post outflow {$id}: " . $e->getMessage());
        return response()->json([
            'message' => 'Failed to generate accounting entries',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Generate accounting entries for multiple post outflows
 */
public function generateBulkAccountingEntries(Request $request)
{
    $validated = $request->validate([
        'post_outflow_ids' => 'required|array',
        'post_outflow_ids.*' => 'integer|exists:post_outflows,id'
    ]);

    $results = [];
    $successCount = 0;
    $failureCount = 0;

    foreach ($validated['post_outflow_ids'] as $postOutflowId) {
        try {
            $postOutflow = PostOutflow::findOrFail($postOutflowId);
            
            // Check if accounting entries already exist
            $existingEntries = TransactionJournalEntry::where('description', 'LIKE', "%Post Outflow #{$postOutflow->id}%")->first();
            
            if ($existingEntries) {
                $results[] = [
                    'post_outflow_id' => $postOutflowId,
                    'status' => 'skipped',
                    'message' => 'Accounting entries already exist'
                ];
                continue;
            }

            $journalEntry = $this->accountingEntryService->generatePostOutflowEntries($postOutflow);
            
            $results[] = [
                'post_outflow_id' => $postOutflowId,
                'status' => 'success',
                'journal_entry_id' => $journalEntry->id
            ];
            $successCount++;
        } catch (\Exception $e) {
            Log::error("Failed to generate accounting entries for post outflow {$postOutflowId}: " . $e->getMessage());
            $results[] = [
                'post_outflow_id' => $postOutflowId,
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
            $failureCount++;
        }
    }

    return response()->json([
        'message' => "Bulk accounting entries generation completed",
        'total_processed' => count($validated['post_outflow_ids']),
        'success_count' => $successCount,
        'failure_count' => $failureCount,
        'results' => $results
    ]);
}

/**
 * Get accounting entries for a specific post outflow
 */
public function getAccountingEntries($id)
{
    $postOutflow = PostOutflow::findOrFail($id);
    
    $journalEntries = TransactionJournalEntry::where('description', 'LIKE', "%Post Outflow #{$postOutflow->id}%")
        ->with('details')
        ->get();

    return response()->json([
        'post_outflow_id' => $id,
        'journal_entries' => $journalEntries
    ]);
}
}