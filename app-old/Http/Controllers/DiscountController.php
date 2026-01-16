<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountStoreRequest;
use App\Http\Requests\DiscountUpdateRequest;
use App\Http\Resources\DiscountCollection;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DiscountController extends Controller
{
    public function index(Request $request): DiscountCollection
    {
        $discounts = Discount::all();

        return new DiscountCollection($discounts);
    }

    public function store(DiscountStoreRequest $request): DiscountResource
    {
        $discount = Discount::create($request->validated());

        return new DiscountResource($discount);
    }

    public function show(Request $request, Discount $discount): DiscountResource
    {
        return new DiscountResource($discount);
    }

    public function update(DiscountUpdateRequest $request, Discount $discount): DiscountResource
    {
        $discount->update($request->validated());

        return new DiscountResource($discount);
    }

  public function destroy($id)
    {   
       
        Discount::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
