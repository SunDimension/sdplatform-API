<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerTypeStoreRequest;
use App\Http\Requests\CustomerTypeUpdateRequest;
use App\Http\Resources\CustomerTypeCollection;
use App\Http\Resources\CustomerTypeResource;
use App\Models\CustomerType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerTypeController extends Controller
{
    public function index(Request $request): CustomerTypeCollection
    {
        $customerTypes = CustomerType::all();

        return new CustomerTypeCollection($customerTypes);
    }

    public function store(CustomerTypeStoreRequest $request): CustomerTypeResource
    {
        $customerType = CustomerType::create($request->validated());

        return new CustomerTypeResource($customerType);
    }

    public function show(Request $request, CustomerType $customerType): CustomerTypeResource
    {
        return new CustomerTypeResource($customerType);
    }

    public function update(CustomerTypeUpdateRequest $request, CustomerType $customerType): CustomerTypeResource
    {
        $customerType->update($request->validated());

        return new CustomerTypeResource($customerType);
    }

   public function destroy($id): Response
    {
       CustomerType::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
