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

class PostInflowController extends Controller
{
 
    public function index(Request $request): PostInflowCollection
    {
        // Validate incoming request parameters
        $validated = $request->validate([
            'bank_id' => 'nullable',  // 
            'from_date' => 'nullable|date',   // 
            'to_date' => 'nullable|date',     // 
        ]);

        // Log the validated input for debugging
      FacadesLog::debug($validated);

    $bankId = $validated['bank_id'];
    $fromDate = $validated['from_date'];
    $toDate = $validated['to_date'];
        // Start building the query for inflows
        $query = PostInflow::query();

        // Filter by bank_id if provided
        if ($bankId) {
            $query->where('bank_id', $bankId);
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

        // Fetch the filtered inflows
        $postInflows = $query->get();

        // Return the inflows as a resource collection
        return new PostInflowCollection($postInflows);
    }
    public function store(PostInflowStoreRequest $request): PostInflowResource
    {
        $postinflow = PostInflow::create($request->validated());

        return new PostInflowResource($postinflow);
    }

    public function show(Request $request, PostInflow $postinflow): PostInflowResource
    {
        return new PostInflowResource($postinflow);
    }

    public function update(PostInflowUpdateRequest $request, PostInflow $postinflow): PostInflowResource
    {
        $postinflow->update($request->validated());

        return new PostInflowResource($postinflow);
    }

   public function destroy($id)
    {   
       
        PostInflow::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}


