<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostOutflowStoreRequest;
use App\Http\Requests\PostOutflowUpdateRequest;
use App\Http\Resources\PostOutflowCollection;
use App\Http\Resources\PostOutflowResource;
use App\Models\PostOutflow;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Validator;
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
        $postoutflow = PostOutflow::create($request->validated());

        return new PostOutflowResource($postoutflow);
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
}