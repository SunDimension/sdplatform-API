<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionTypeStoreRequest;
use App\Http\Requests\TransactionTypeUpdateRequest;
use App\Http\Resources\TransactionTypeCollection;
use App\Http\Resources\TransactionTypeResource;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactiontype = TransactionType::all();

        return new TransactionTypeCollection($transactiontype);
    }

    public function store(TransactionTypeStoreRequest $request)
    {
        $transactiontype = TransactionType::create($request->validated());

        return new TransactionTypeResource($transactiontype);
    }

    public function show(Request $request, TransactionType $transactiontype)
    {
        return new TransactionTypeResource($transactiontype);
    }

    public function update(TransactionTypeUpdateRequest $request, TransactionType $transactiontype)
    {
        $transactiontype->update($request->validated());

        return new TransactionTypeResource($transactiontype);
    }

    public function destroy(Request $request, TransactionType $transactiontype)
    {
        $transactiontype->delete();

        return response()->noContent();
    }
}

