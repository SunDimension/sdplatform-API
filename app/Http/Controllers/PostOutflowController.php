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
use Illuminate\Support\Log;

class PostOutflowController extends Controller
{
  
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
}