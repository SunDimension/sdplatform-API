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
        $data['inflow_status'] = 6; // Assuming "Unclaimed" has an ID of 1

        $postinflow = PostInflow::create($data);

        return new PostInflowResource($postinflow);
    }

    public function show(Request $request, PostInflow $postinflow): PostInflowResource
    {
        return new PostInflowResource($postinflow);
    }

    public function update(PostInflowUpdateRequest $request, $post_inflow): PostInflowResource
    {
        // FacadesLog::debug('Route Parameters: ' . json_encode(request()->route()->parameters()));
        // FacadesLog::debug('PostInflow Instance: ' . json_encode($postinflow));
        // FacadesLog::debug('Validated Data: ' . json_encode($request->validated()));

        $postinflow = PostInflow::findOrFail($post_inflow);

        $data = $request->validated();
        if(isset($data['customer_id']) && $data['inflow_status']==6 )
        {
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
}
