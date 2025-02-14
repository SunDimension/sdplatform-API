<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReasonStoreRequest;
use App\Http\Requests\ReasonUpdateRequest;
use App\Http\Resources\ReasonCollection;
use App\Http\Resources\ReasonResource;
use App\Models\Reason;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReasonController extends Controller
{
    public function index(Request $request): ReasonCollection
    {
        $reason = Reason::all();

        return new ReasonCollection($reason);
    }
    public function store(ReasonStoreRequest $request): ReasonResource
    {
        $reason = Reason::create($request->validated());

        return new ReasonResource($reason);
    }

    public function show(Request $request, Reason $reason): ReasonResource
    {
        return new ReasonResource($reason);
    }

    public function update(ReasonUpdateRequest $request, Reason $reason): ReasonResource
    {
        $reason->update($request->validated());

        return new ReasonResource($reason);
    }

   public function destroy($id)
    {   
       
        Reason::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

