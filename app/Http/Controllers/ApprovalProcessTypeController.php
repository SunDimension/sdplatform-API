<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalProcessTypeStoreRequest;
use App\Http\Requests\ApprovalProcessTypeUpdateRequest;
use App\Http\Resources\ApprovalProcessTypeCollection;
use App\Http\Resources\ApprovalProcessTypeResource;
use App\Models\ApprovalProcessType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApprovalProcessTypeController extends Controller
{
    public function index(Request $request)
    {
        $approvalProcessTypes = ApprovalProcessType::all();

        return new ApprovalProcessTypeCollection($approvalProcessTypes);
    }

    public function store(ApprovalProcessTypeStoreRequest $request)
    {
        $approvalProcessType = ApprovalProcessType::create($request->validated());

        return new ApprovalProcessTypeResource($approvalProcessType);
    }

    public function show(Request $request, ApprovalProcessType $approvalProcessType)
    {
        return new ApprovalProcessTypeResource($approvalProcessType);
    }

    public function update(ApprovalProcessTypeUpdateRequest $request, ApprovalProcessType $approvalProcessType)
    {
        $approvalProcessType->update($request->validated());

        return new ApprovalProcessTypeResource($approvalProcessType);
    }

    public function destroy(Request $request, ApprovalProcessType $approvalProcessType)
    {
        $approvalProcessType->delete();

        return response()->noContent();
    }
}
