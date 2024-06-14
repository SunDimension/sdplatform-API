<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditSaleStoreRequest;
use App\Http\Requests\CreditSaleUpdateRequest;
use App\Http\Resources\CreditSaleCollection;
use App\Http\Resources\CreditSaleResource;
use App\Models\CreditSale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreditSaleController extends Controller
{
    public function index(Request $request): CreditSaleCollection
    {
        $creditSales = CreditSale::all();

        return new CreditSaleCollection($creditSales);
    }

    public function store(CreditSaleStoreRequest $request): CreditSaleResource
    {
        $creditSale = CreditSale::create($request->validated());

        return new CreditSaleResource($creditSale);
    }

    public function show(Request $request, CreditSale $creditSale): CreditSaleResource
    {
        return new CreditSaleResource($creditSale);
    }

    public function update(CreditSaleUpdateRequest $request, CreditSale $creditSale): CreditSaleResource
    {
        $creditSale->update($request->validated());

        return new CreditSaleResource($creditSale);
    }

    public function destroy(Request $request, CreditSale $creditSale): Response
    {
        $creditSale->delete();

        return response()->noContent();
    }
}
