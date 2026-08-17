<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatesStoreRequest;
use App\Http\Requests\StatesUpdateRequest;
use App\Http\Resources\StateCollection;
use App\Http\Resources\StateResource;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatesController extends Controller
{
    public function index(Request $request): StateCollection
    {
        $states = State::all();

        return new StateCollection($states);
    }

    public function store(StatesStoreRequest $request): StateResource
    {
        $state = State::create($request->validated());

        return new StateResource($state);
    }

    public function show(Request $request, State $state): StateResource
    {
        return new StateResource($state);
    }

    public function update(StatesUpdateRequest $request, State $state): StateResource
    {
        $state->update($request->validated());

        return new StateResource($state);
    }

    public function destroy(Request $request, State $state): Response
    {
        $state->delete();

        return response()->noContent();
    }
}
