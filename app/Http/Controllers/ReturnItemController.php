<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnItemStoreRequest;
use App\Http\Requests\ReturnItemUpdateRequest;
use App\Http\Resources\ReturnItemCollection;
use App\Http\Resources\ReturnItemResource;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReturnItemController extends Controller
{
    public function index(Request $request): ReturnItemCollection
    {
        $returnItem = ReturnItem::all();

        return new ReturnItemCollection($returnItem);
    }
    public function store(ReturnItemStoreRequest $request): ReturnItemResource
    {
        $returnItem = ReturnItem::create($request->validated());

        return new ReturnItemResource($returnItem);
    }

    public function show(Request $request, ReturnItem $returnItem): ReturnItemResource
    {
        return new ReturnItemResource($returnItem);
    }

    public function update(ReturnItemUpdateRequest $request, ReturnItem $returnItem): ReturnItemResource
    {
        $returnItem->update($request->validated());

        return new ReturnItemResource($returnItem);
    }

   public function destroy($id)
    {   
       
        ReturnItem::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
