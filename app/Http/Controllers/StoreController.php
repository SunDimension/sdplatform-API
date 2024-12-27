<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\StoreUpdateRequest;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreController extends Controller
{
    public function index(Request $request): StoreCollection
    {
        $store = Store::all();

        return new StoreCollection($store);
    }

    public function mystore(Request $request): StoreCollection
    {
        //$store = Store::all();
        $store = Store::where('branch_id', auth()->user()->branch_id)->get();

        return new StoreCollection($store);
    }

    public function mystorewithitems(Request $request): StoreCollection
    {
        //$store = Store::all();
        $store = Store::where('branch_id', auth()->user()->branch_id)->get();
        return new StoreCollection($store->load('storeItems'));
    }

    public function mystore2($branchId): StoreCollection
    {
        $store = Store::where('branch_id', $branchId)->get();

        return new StoreCollection($store);
    }

    public function store(StoreStoreRequest $request): StoreResource
    {
        $store = Store::create($request->validated());
        return new StoreResource($store);
    }

    public function show(Request $request, Store $store): StoreResource
    {
        return new StoreResource($store);
    }

    public function update(StoreUpdateRequest $request, Store $store): StoreResource
    {
        $store->update($request->validated());

        return new StoreResource($store);
    }

   public function destroy($id)
    {   
       
        Store::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
