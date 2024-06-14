<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPurchaseReceivedStoreRequest;
use App\Http\Requests\NewPurchaseReceivedUpdateRequest;
use App\Http\Resources\NewPurchaseReceivedCollection;
use App\Http\Resources\NewPurchaseReceivedResource;
use App\Models\NewPurchaseReceived;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NewPurchaseReceivedController extends Controller
{
    public function index(Request $request): NewPurchaseReceivedCollection
    {
        $newPurchaseReceiveds = NewPurchaseReceived::all();

        return new NewPurchaseReceivedCollection($newPurchaseReceiveds);
    }

    public function store(NewPurchaseReceivedStoreRequest $request): NewPurchaseReceivedResource
    {
        $newPurchaseReceived = NewPurchaseReceived::create($request->validated());

        return new NewPurchaseReceivedResource($newPurchaseReceived);
    }

    public function show(Request $request, NewPurchaseReceived $newPurchaseReceived): NewPurchaseReceivedResource
    {
        return new NewPurchaseReceivedResource($newPurchaseReceived);
    }

    public function update(NewPurchaseReceivedUpdateRequest $request, NewPurchaseReceived $newPurchaseReceived): NewPurchaseReceivedResource
    {
        $newPurchaseReceived->update($request->validated());

        return new NewPurchaseReceivedResource($newPurchaseReceived);
    }

    public function destroy(Request $request, NewPurchaseReceived $newPurchaseReceived): Response
    {
        $newPurchaseReceived->delete();

        return response()->noContent();
    }
}
