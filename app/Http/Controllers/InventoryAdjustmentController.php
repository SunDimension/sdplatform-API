<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryAdjustmentStoreRequest;
use App\Http\Requests\InventoryAdjustmentUpdateRequest;
use App\Http\Resources\InventoryAdjustmentCollection;
use App\Http\Resources\InventoryAdjustmentResource;
use App\Models\InventoryAdjustment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InventoryAdjustmentController extends Controller
{
    public function index(Request $request): InventoryAdjustmentCollection
    {
        $inventoryAdjustments = InventoryAdjustment::all();

        return new InventoryAdjustmentCollection($inventoryAdjustments);
    }

    public function store(InventoryAdjustmentStoreRequest $request): InventoryAdjustmentResource
    {
        $inventoryAdjustment = InventoryAdjustment::create($request->validated());

        return new InventoryAdjustmentResource($inventoryAdjustment);
    }

    public function show(Request $request, InventoryAdjustment $inventoryAdjustment): InventoryAdjustmentResource
    {
        return new InventoryAdjustmentResource($inventoryAdjustment);
    }

    public function update(InventoryAdjustmentUpdateRequest $request, InventoryAdjustment $inventoryAdjustment): InventoryAdjustmentResource
    {
        $inventoryAdjustment->update($request->validated());

        return new InventoryAdjustmentResource($inventoryAdjustment);
    }
    
    public function destroy($id): Response
    {   
       
        InventoryAdjustment::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
