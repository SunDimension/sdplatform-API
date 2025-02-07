<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalProcessFlowStoreRequest;
use App\Http\Requests\ApprovalProcessFlowUpdateRequest;
use App\Http\Resources\ApprovalProcessFlowCollection;
use App\Http\Resources\ApprovalProcessFlowResource;
use App\Models\ApprovalProcessFlow;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApprovalProcessFlowController extends Controller
{
    public function index(Request $request)
    {
        $approvalProcessFlows = ApprovalProcessFlow::all();

        return new ApprovalProcessFlowCollection($approvalProcessFlows);
    }

    public function store(ApprovalProcessFlowStoreRequest $request)
    {
        $approvalProcessFlow = ApprovalProcessFlow::create($request->validated());

        return new ApprovalProcessFlowResource($approvalProcessFlow);
    }

    public function show(Request $request, ApprovalProcessFlow $approvalProcessFlow)
    {
        return new ApprovalProcessFlowResource($approvalProcessFlow);
    }

    public function update(ApprovalProcessFlowUpdateRequest $request, ApprovalProcessFlow $approvalProcessFlow)
    {
        $approvalProcessFlow->update($request->validated());

        return new ApprovalProcessFlowResource($approvalProcessFlow);
    }

    public function destroy(Request $request, ApprovalProcessFlow $approvalProcessFlow)
    {
        $approvalProcessFlow->delete();

        return response()->noContent();
    }
}
