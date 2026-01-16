<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettleCreditStoreRequest;
use App\Http\Requests\SettleCreditUpdateRequest;
use App\Http\Resources\SettleCreditCollection;
use App\Http\Resources\SettleCreditResource;
use App\Models\SettleCredit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettleCreditController extends Controller
{
    public function index(Request $request): SettleCreditCollection
    {
        $settlecredit = SettleCredit::all();

        return new SettleCreditCollection($settlecredit);
    }
    public function store(SettleCreditStoreRequest $request): SettleCreditResource
    {
        $settlecredit = SettleCredit::create($request->validated());

        return new SettleCreditResource($settlecredit);
    }

    public function show(Request $request, SettleCredit $settlecredit): SettleCreditResource
    {
        return new SettleCreditResource($settlecredit);
    }

    public function update(SettleCreditUpdateRequest $request, SettleCredit $settlecredit): SettleCreditResource
    {
        $settlecredit->update($request->validated());

        return new SettleCreditResource($settlecredit);
    }

   public function destroy($id)
    {   
       
        SettleCredit::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

