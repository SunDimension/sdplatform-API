<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReleaseDetailsStoreRequest;
use App\Http\Requests\ReleaseDetailsUpdateRequest;
use App\Http\Resources\ReleaseDetailsCollection;
use App\Http\Resources\ReleaseDetailsResource;
use App\Models\ReleaseDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReleaseDetailsController extends Controller
{
     public function index(Request $request): ReleaseDetailsCollection
    {
        $releaseDetails = ReleaseDetails::all();

        return new ReleaseDetailsCollection($releaseDetails);
    }
    public function store(ReleaseDetailsStoreRequest $request): ReleaseDetailsResource
    {
        $releaseDetails = ReleaseDetails::create($request->validated());

        return new ReleaseDetailsResource($releaseDetails);
    }

    public function show(Request $request, ReleaseDetails $releaseDetails): ReleaseDetailsResource
    {
        return new ReleaseDetailsResource($releaseDetails);
    }

    public function update(ReleaseDetailsUpdateRequest $request, ReleaseDetails $releaseDetails): ReleaseDetailsResource
    {
        $releaseDetails->update($request->validated());

        return new ReleaseDetailsResource($releaseDetails);
    }

   public function destroy($id)
    {   
       
        ReleaseDetails::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
