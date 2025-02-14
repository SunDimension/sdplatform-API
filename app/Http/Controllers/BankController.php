<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankStoreRequest;
use App\Http\Requests\BankUpdateRequest;
use App\Http\Resources\BankCollection;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankController extends Controller
{
    public function index(Request $request): BankCollection
    {
        $banks = Bank::all();

        return new BankCollection($banks);
    }

    public function store(BankStoreRequest $request): BankResource
    {
        $bank = Bank::create($request->validated());

        return new BankResource($bank);
    }

    public function show(Request $request, Bank $bank): BankResource
    {
        return new BankResource($bank);
    }

    public function update(BankUpdateRequest $request, Bank $bank): BankResource
    {
        $bank->update($request->validated());

        return new BankResource($bank);
    }

    public function destroy($id)
    {   
       
        Bank::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
