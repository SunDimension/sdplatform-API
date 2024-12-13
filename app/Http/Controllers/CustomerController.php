<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerExtendedCollection;
use App\Http\Resources\CustomerExtendedResource;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index(Request $request): CustomerCollection
    {
        // Check if the user can view customers
        $user = auth()->user();
        if (Gate::allows('Can view All Customers')) {
            $customers = Customer::all();
            Log::alert('ED');
            return new CustomerCollection($customers);
        } elseif (Gate::allows('Can see branch customers')) {
            Log::alert('ED3');
            $customers = Customer::where('branch_id', $user->branch_id)->get();
            return new CustomerCollection($customers);
        }

        return new CustomerCollection([]);
    }

    public function balances()
    {
        $user = auth()->user();
        if (Gate::allows('Can View All Customers Balance')) {

            $customers = Customer::withSum(['creditTransactions as total_payment' => function ($query) {
                $query->where('type', 'payment');
            }], 'amount')
                ->withSum(['creditTransactions as total_credit' => function ($query) {
                    $query->where('type', 'credit');
                }], 'amount')
                ->withSum('inflows as total_inflow', 'amount')
                ->withSum('outflows as total_outflow', 'amount')
                ->get();

            
            return new CustomerExtendedCollection($customers);
        } elseif (Gate::allows('Can see branch customers Balance')) {
            Log::alert('ED3');

            $customers = Customer::where('branch_id', $user->branch_id)
                ->withSum(['creditTransactions as total_payment' => function ($query) {
                    $query->where('type', 'payment');
                }], 'amount')
                ->withSum(['creditTransactions as total_credit' => function ($query) {
                    $query->where('type', 'credit');
                }], 'amount')
                ->withSum('inflows as total_inflow', 'amount')
                ->withSum('outflows as total_outflow', 'amount')
                ->get();

           

            return new CustomerExtendedCollection($customers);
        }
        // Log::info("Da", $orders);
        return new CustomerExtendedCollection([]);
    }

    public function customerBalanceHistory($id)
    {
        $user = auth()->user();
        $id = 10;
        $id = 10;

        $customer = Customer::with([
            'creditTransactions',
            'inflows',
            'outflows',
        ])
            ->where('id', $id)
            ->withSum(['creditTransactions as total_payment' => function ($query) {
                $query->where('type', 'payment');
            }], 'amount')
            ->withSum(['creditTransactions as total_credit' => function ($query) {
                $query->where('type', 'credit');
            }], 'amount')
            ->withSum('inflows as total_inflow', 'amount')
            ->withSum('outflows as total_outflow', 'amount')
            ->first();

        // new CustomerExtendedResource($customer);
        // Log::info("Da", $orders);
        return new CustomerExtendedResource($customer);
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
        $this->authorize('view-customer');
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
