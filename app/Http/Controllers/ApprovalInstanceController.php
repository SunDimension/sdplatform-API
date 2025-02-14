<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalInstanceStoreRequest;
use App\Http\Requests\ApprovalInstanceUpdateRequest;
use App\Http\Resources\ApprovalInstanceCollection;
use App\Http\Resources\ApprovalInstanceResource;
use App\Models\ApprovalInstance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApprovalInstanceController extends Controller
{
    public function index(Request $request)
    {
        $approvalInstances = ApprovalInstance::all();

        return new ApprovalInstanceCollection($approvalInstances);
    }

    public function store(ApprovalInstanceStoreRequest $request)
    {
        $approvalInstance = ApprovalInstance::create($request->validated());

        return new ApprovalInstanceResource($approvalInstance);
    }

    public function show(Request $request, ApprovalInstance $approvalInstance)
    {
        return new ApprovalInstanceResource($approvalInstance);
    }

    public function update(ApprovalInstanceUpdateRequest $request, ApprovalInstance $approvalInstance)
    {
        $approvalInstance->update($request->validated());

        return new ApprovalInstanceResource($approvalInstance);
    }

    public function destroy(Request $request, ApprovalInstance $approvalInstance)
    {
        $approvalInstance->delete();

        return response()->noContent();
    }
}
