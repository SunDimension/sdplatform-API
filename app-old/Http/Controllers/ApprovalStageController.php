<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalStageStoreRequest;
use App\Http\Requests\ApprovalStageUpdateRequest;
use App\Http\Resources\ApprovalStageCollection;
use App\Http\Resources\ApprovalStageResource;
use App\Models\ApprovalStage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApprovalStageController extends Controller
{
    public function index(Request $request)
    {
        $approvalStages = ApprovalStage::all();

        return new ApprovalStageCollection($approvalStages);
    }

    public function store(ApprovalStageStoreRequest $request)
    {
        $approvalStage = ApprovalStage::create($request->validated());

        return new ApprovalStageResource($approvalStage);
    }

    public function show(Request $request, ApprovalStage $approvalStage)
    {
        return new ApprovalStageResource($approvalStage);
    }

    public function update(ApprovalStageUpdateRequest $request, ApprovalStage $approvalStage)
    {
        $approvalStage->update($request->validated());

        return new ApprovalStageResource($approvalStage);
    }

    public function destroy(Request $request, ApprovalStage $approvalStage)
    {
        $approvalStage->delete();

        return response()->noContent();
    }
}
