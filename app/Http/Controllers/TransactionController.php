<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::all();

        return new TransactionCollection($transactions);
    }

    public function store(TransactionStoreRequest $request)
    {
        $transaction = Transaction::create($request->validated());

        return new TransactionResource($transaction);
    }

    public function show(Request $request, Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    public function update(TransactionUpdateRequest $request, Transaction $transaction)
    {
        $transaction->update($request->validated());

        return new TransactionResource($transaction);
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        $transaction->delete();

        return response()->noContent();
    }

    public function searchTransaction(Request $request)
    {
        $validated = $request->validate([
            'transaction_number' => 'nullable|string',
            'transaction_type' => 'nullable|string',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $query = Transaction::query();

        if (!empty($validated['transaction_number'])) {
            $query->where('transaction_number', 'like', '%' . $validated['transaction_number'] . '%');
        }

        if (!empty($validated['transaction_type'])) {
            $query->where('transaction_type', $validated['transaction_type']);
        }

        if (!empty($validated['from_date']) && !empty($validated['to_date'])) {
            $query->whereBetween('transaction_date', [
                Carbon::parse($validated['from_date'])->startOfDay(),
                Carbon::parse($validated['to_date'])->endOfDay(),
            ]);
        }

        return new TransactionCollection($query->orderBy('transaction_date', 'desc')->get());
    }
}