<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesOrderStoreRequest;
use App\Http\Requests\SalesOrderUpdateRequest;
use App\Http\Resources\SalesOrderCollection;
use App\Http\Resources\SalesOrderResource;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesOrderController extends Controller
{
    
    public function index(Request $request): SalesOrderCollection
    {
        $salesorder = SalesOrder::all();

        return new SalesOrderCollection($salesorder);
    }
    public function store(SalesOrderStoreRequest $request): SalesOrderResource
    {
        $salesorder = SalesOrder::create($request->validated());

        return new SalesOrderResource($salesorder);
    }

    public function show(Request $request, SalesOrder $salesorder): SalesOrderResource
    {
        return new SalesOrderResource($salesorder);
    }

    public function update(SalesOrderUpdateRequest $request, SalesOrder $salesorder): SalesOrderResource
    {
        $salesorder->update($request->validated());

        return new SalesOrderResource($salesorder);
    }

   public function destroy($id)
    {   
       
        SalesOrder::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
