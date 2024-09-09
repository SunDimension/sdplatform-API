<?php

namespace App\Http\Controllers;

use App\Http\Requests\StateStoreRequest;
use App\Http\Requests\StateUpdateRequest;
use App\Http\Resources\StateCollection;
use App\Http\Resources\StateResource;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StateController extends Controller
{
    public function index(Request $request): StateCollection
    {
        $states = State::all();

        return new StateCollection($states);
    }

    public function store(StateStoreRequest $request): StateResource
    {
        $state = State::create($request->validated());

        return new StateResource($state);
    }

    public function show(Request $request, State $state): StateResource
    {
        return new StateResource($state);
    }

    public function update(StateUpdateRequest $request, State $state): StateResource
    {
        $state->update($request->validated());

        return new StateResource($state);
    }

  public function destroy($id)
    {   
       
        State::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
