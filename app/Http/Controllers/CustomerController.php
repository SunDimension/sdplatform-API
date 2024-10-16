<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function index(Request $request): CustomerCollection
    {
        $customers = Customer::all();
        return new CustomerCollection($customers);
    }

    public function store(CustomerStoreRequest $request): CustomerResource
    {
        // Get validated data
        $data = $request->validated();

        // Concatenate surname and middlename to create name
        $data['name'] = trim($data['surname'] . ' ' . ($data['middlename'] ?? ''));

        // Create the new customer with the modified data
        $customer = Customer::create($data);

        return new CustomerResource($customer);
    }

    public function show(Request $request, Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }

    public function update(CustomerUpdateRequest $request, Customer $customer): CustomerResource
    {
        // Get validated data
        $data = $request->validated();

        // Concatenate surname and middlename to create name
        $data['name'] = trim($data['surname'] . ' ' . ($data['middlename'] ?? ''));

        // Update the customer with the modified data
        $customer->update($data);

        return new CustomerResource($customer);
    }

    public function destroy($id)
    {
        Customer::destroy($id);
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
