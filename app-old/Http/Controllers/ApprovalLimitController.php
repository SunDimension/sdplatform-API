<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalLimitStoreRequest;
use App\Http\Requests\ApprovalLimitUpdateRequest;
use App\Http\Resources\ApprovalLimitCollection;
use App\Http\Resources\ApprovalLimitResource;
use App\Models\ApprovalLimit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApprovalLimitController extends Controller
{
    public function index(Request $request)
    {
        $approvalLimits = ApprovalLimit::all();

        return new ApprovalLimitCollection($approvalLimits);
    }

    public function store(ApprovalLimitStoreRequest $request)
    {
        $approvalLimit = ApprovalLimit::create($request->validated());

        return new ApprovalLimitResource($approvalLimit);
    }

    public function show(Request $request, ApprovalLimit $approvalLimit)
    {
        return new ApprovalLimitResource($approvalLimit);
    }

    public function update(ApprovalLimitUpdateRequest $request, ApprovalLimit $approvalLimit)
    {
        $approvalLimit->update($request->validated());

        return new ApprovalLimitResource($approvalLimit);
    }

    public function destroy(Request $request, ApprovalLimit $approvalLimit)
    {
        $approvalLimit->delete();

        return response()->noContent();
    }
}
