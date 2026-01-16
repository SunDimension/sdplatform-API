<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeReasonStoreRequest;
use App\Http\Requests\ChangeReasonUpdateRequest;
use App\Http\Resources\ChangeReasonCollection;
use App\Http\Resources\ChangeReasonResource;
use App\Models\ChangeReason;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChangeReasonController extends Controller
{
    public function index(Request $request)
    {
        $changeReasons = ChangeReason::all();

        return new ChangeReasonCollection($changeReasons);
    }

    public function store(ChangeReasonStoreRequest $request)
    {
        $changeReason = ChangeReason::create($request->validated());

        return new ChangeReasonResource($changeReason);
    }

    public function show(Request $request, ChangeReason $changeReason)
    {
        return new ChangeReasonResource($changeReason);
    }

    public function update(ChangeReasonUpdateRequest $request, ChangeReason $changeReason)
    {
        $changeReason->update($request->validated());

        return new ChangeReasonResource($changeReason);
    }

    public function destroy(Request $request, ChangeReason $changeReason)
    {
        $changeReason->delete();

        return response()->noContent();
    }
}
