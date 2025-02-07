<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalProcessModuleStoreRequest;
use App\Http\Requests\ApprovalProcessModuleUpdateRequest;
use App\Http\Resources\ApprovalProcessModuleCollection;
use App\Http\Resources\ApprovalProcessModuleResource;
use App\Models\ApprovalProcessModule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApprovalProcessModuleController extends Controller
{
    public function index(Request $request)
    {
        $approvalProcessModules = ApprovalProcessModule::all();

        return new ApprovalProcessModuleCollection($approvalProcessModules);
    }

    public function store(ApprovalProcessModuleStoreRequest $request)
    {
        $approvalProcessModule = ApprovalProcessModule::create($request->validated());

        return new ApprovalProcessModuleResource($approvalProcessModule);
    }

    public function show(Request $request, ApprovalProcessModule $approvalProcessModule)
    {
        return new ApprovalProcessModuleResource($approvalProcessModule);
    }

    public function update(ApprovalProcessModuleUpdateRequest $request, ApprovalProcessModule $approvalProcessModule)
    {
        $approvalProcessModule->update($request->validated());

        return new ApprovalProcessModuleResource($approvalProcessModule);
    }

    public function destroy(Request $request, ApprovalProcessModule $approvalProcessModule)
    {
        $approvalProcessModule->delete();

        return response()->noContent();
    }
}
