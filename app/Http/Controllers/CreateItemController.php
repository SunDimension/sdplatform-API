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
    public function index(Request $request): CreateItemCollection
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
        

        return new CreateItemCollection($createItem);
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

//    public function search(Request $request): CreateItemCollection
// {
//     $sname = $request->input('name', '');

//     if (!empty($sname)) {
//         // Assuming `name` is a column in `create_items`
//         $createItems = CreateItem::where('name', 'like', "%$sname%")
//             ->where('user_id', Auth::id())
//             ->get();
//     } else {
//         // Return all items for the authenticated user if no name is provided
//         $createItems = CreateItem::where('user_id', Auth::id())->get();
//     }

//     return new CreateItemCollection($createItems);
// }
}
