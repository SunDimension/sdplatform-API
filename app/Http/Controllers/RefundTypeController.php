<?php

namespace App\Http\Controllers;

use App\Http\Requests\RefundTypeStoreRequest;
use App\Http\Requests\RefundTypeUpdateRequest;
use App\Http\Resources\RefundTypeCollection;
use App\Http\Resources\RefundTypeResource;
use App\Models\RefundType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefundTypeController extends Controller
{
    public function index(Request $request): RefundTypeCollection
    {
        $refundtype = RefundType::all();

        return new RefundTypeCollection($refundtype);
    }
    public function store(RefundTypeStoreRequest $request): RefundTypeResource
    {
        $refundtype = RefundType::create($request->validated());

        return new RefundTypeResource($refundtype);
    }

    public function show(Request $request, RefundType $refundtype): RefundTypeResource
    {
        return new RefundTypeResource($refundtype);
    }

    public function update(RefundTypeUpdateRequest $request, RefundType $refundtype): RefundTypeResource
    {
        $refundtype->update($request->validated());

        return new RefundTypeResource($refundtype);
    }

   public function destroy($id)
    {   
       
        RefundType::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

