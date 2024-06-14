<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPurchaseReceiveStoreRequest;
use App\Http\Requests\NewPurchaseReceiveUpdateRequest;
use App\Http\Resources\NewPurchaseReceifeCollection;
use App\Http\Resources\NewPurchaseReceiveResource;
use App\NewPurchaseReceive;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NewPurchaseReceiveController extends Controller
{
    public function index(Request $request): NewPurchaseReceifeCollection
    {
        $newPurchaseReceives = NewPurchaseReceive::all();

        return new NewPurchaseReceifeCollection($newPurchaseReceives);
    }

    public function store(NewPurchaseReceiveStoreRequest $request): NewPurchaseReceiveResource
    {
        $newPurchaseReceive = NewPurchaseReceive::create($request->validated());

        return new NewPurchaseReceiveResource($newPurchaseReceive);
    }

    public function show(Request $request, NewPurchaseReceive $newPurchaseReceive): NewPurchaseReceiveResource
    {
        return new NewPurchaseReceiveResource($newPurchaseReceive);
    }

    public function update(NewPurchaseReceiveUpdateRequest $request, NewPurchaseReceive $newPurchaseReceive): NewPurchaseReceiveResource
    {
        $newPurchaseReceive->update($request->validated());

        return new NewPurchaseReceiveResource($newPurchaseReceive);
    }

    public function destroy(Request $request, NewPurchaseReceive $newPurchaseReceive): Response
    {
        $newPurchaseReceive->delete();

        return response()->noContent();
    }
}
