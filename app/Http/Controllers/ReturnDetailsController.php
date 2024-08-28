<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnDetailsStoreRequest;
use App\Http\Requests\ReturnDetailsUpdateRequest;
use App\Http\Resources\ReturnDetailsCollection;
use App\Http\Resources\ReturnDetailsResource;
use App\Models\ReturnDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReturnDetailsController extends Controller
{
    public function index(Request $request): ReturnDetailsCollection
    {
        $returnDetails = ReturnDetails::all();

        return new ReturnDetailsCollection($returnDetails);
    }
    public function store(ReturnDetailsStoreRequest $request): ReturnDetailsResource
    {
        $returnDetails = ReturnDetails::create($request->validated());

        return new ReturnDetailsResource($returnDetails);
    }

    public function show(Request $request, ReturnDetails $returnDetails): ReturnDetailsResource
    {
        return new ReturnDetailsResource($returnDetails);
    }

    public function update(ReturnDetailsUpdateRequest $request, ReturnDetails $returnDetails): ReturnDetailsResource
    {
        $returnDetails->update($request->validated());

        return new ReturnDetailsResource($returnDetails);
    }

   public function destroy($id): Response
    {   
       
        ReturnDetails::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
