<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemStoreRequest;
use App\Http\Requests\CreateItemUpdateRequest;
use App\Http\Resources\CreateItemCollection;
use App\Http\Resources\CreateItemResource;
use App\Models\CreateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CreateItemController extends Controller
{
    // public function index(Request $request): CreateItemCollection
    // {   
    //     $sname = $request->name;
    //     if($sname !==null && $sname !==''){

    //         $createItems = CreateItem::where('id','=',Auth::user()->id)->get();
    //     }
    //     else
    //     {
    //          $createItem = DB::select("SELECT  name,quantity  FROM create_items Where create_items.`name` like '%$sname%'");

    //         $createItems = $createItem;
    //     }
        

    //     return new CreateItemCollection($createItems);
    // }
    
public function index(Request $request): CreateItemCollection
{
    $name = $request->input('name'); // safer way to get request data

    if ($name) {
        // Query the database using query builder to prevent SQL injection
        $createItems = CreateItem::where('id', Auth::id()) // Use Auth::id() for the current user ID
            ->where('name', 'like', '%' . $name . '%') // Filter by name
            ->get();
    } else {
        // If name is not provided, you might want to return all items or handle this case
        $createItems = CreateItem::where('id', Auth::id())->get();
    }

    return new CreateItemCollection($createItems);
}


    public function store(CreateItemStoreRequest $request): CreateItemResource
    {
        $createItem = CreateItem::create($request->validated());

        return new CreateItemResource($createItem);
    }

    public function show(Request $request, CreateItem $createItem): CreateItemResource
    {
        return new CreateItemResource($createItem);
    }

    public function update(CreateItemUpdateRequest $request, CreateItem $createItem): CreateItemResource
    {
        $createItem->update($request->validated());

        return new CreateItemResource($createItem);
    }

    public function destroy($id): Response
    {
        CreateItem::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function search(Request $request): CreateItemCollection
    {   
        $sname = $request->name;
        if($sname !==null && $sname !==''){

            $createItems = CreateItem::where('id','=',Auth::user()->id)->get();
        }
        else
        {
             $createItem = DB::select("SELECT  name,quantity  FROM create_items Where create_items.`name` like '%$sname%'");

            $createItems = $createItem;
        }
        

        return new CreateItemCollection($createItems);
    }
}
