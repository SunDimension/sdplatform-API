<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchStoreRequest;
use App\Http\Requests\BranchUpdateRequest;
use App\Http\Resources\BranchCollection;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BranchController extends Controller
{
    public function index(Request $request): BranchCollection
    {
        $branches = Branch::all();

        return new BranchCollection($branches);
    }

    public function store(BranchStoreRequest $request): BranchResource
    {
        $branch = Branch::create($request->validated());

        return new BranchResource($branch);
    }

    public function show(Request $request, Branch $branch): BranchResource
    {
        return new BranchResource($branch);
    }

    public function update(BranchUpdateRequest $request, Branch $branch): BranchResource
    {
        $branch->update($request->validated());

        return new BranchResource($branch);
    }

    public function destroy($id)
    {
        // $branch->delete();
        Branch::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
