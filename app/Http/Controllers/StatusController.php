<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusStoreRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Http\Resources\StatusCollection;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatusController extends Controller
{
    public function index(Request $request): StatusCollection
    {
        $statuses = Status::all();

        return new StatusCollection($statuses);
    }

    public function store(StatusStoreRequest $request): StatusResource
    {
        $status = Status::create($request->validated());

        return new StatusResource($status);
    }

    public function show(Request $request, Status $status): StatusResource
    {
        return new StatusResource($status);
    }

    public function update(StatusUpdateRequest $request, Status $status): StatusResource
    {
        $status->update($request->validated());

        return new StatusResource($status);
    }

   public function destroy($id): Response
    {   
       
        Status::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
