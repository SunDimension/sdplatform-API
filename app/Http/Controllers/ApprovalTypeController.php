<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalTypeStoreRequest;
use App\Http\Requests\ApprovalTypeUpdateRequest;
use App\Http\Resources\ApprovalTypeCollection;
use App\Http\Resources\ApprovalTypeResource;
use App\Models\ApprovalType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApprovalTypeController extends Controller
{
    public function index(Request $request)
    {
        $approvalTypes = ApprovalType::all();

        return new ApprovalTypeCollection($approvalTypes);
    }

    public function store(ApprovalTypeStoreRequest $request)
    {
        $approvalType = ApprovalType::create($request->validated());

        return new ApprovalTypeResource($approvalType);
    }

    public function show(Request $request, ApprovalType $approvalType)
    {
        return new ApprovalTypeResource($approvalType);
    }

    public function update(ApprovalTypeUpdateRequest $request, ApprovalType $approvalType)
    {
        $approvalType->update($request->validated());

        return new ApprovalTypeResource($approvalType);
    }

    public function destroy(Request $request, ApprovalType $approvalType)
    {
        $approvalType->delete();

        return response()->noContent();
    }
}
