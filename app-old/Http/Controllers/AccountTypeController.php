<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Http\Requests\AccountTypeStoreRequest;
use App\Http\Requests\AccountTypeUpdateRequest;
use App\Http\Resources\AccountTypeResource;
use Illuminate\Support\Str;

class AccountTypeController extends Controller
{
    // List all account types
    public function index()
    {
        $types = AccountType::all();
        return AccountTypeResource::collection($types);
    }

    // Store new account type
    public function store(AccountTypeStoreRequest $request)
    {
        $type = AccountType::create([
            'account_type_id' => Str::uuid(),
            'account_type' => $request->account_type,
        ]);

        return new AccountTypeResource($type);
    }

    // Show a single account type
    public function show(AccountType $accountType)
    {
        return new AccountTypeResource($accountType);
    }

    // Update an account type
    public function update(AccountTypeUpdateRequest $request, AccountType $accountType)
    {
        $accountType->update([
            'account_type' => $request->account_type,
        ]);

        return new AccountTypeResource($accountType);
    }

    // Delete an account type
    public function destroy(AccountType $accountType)
    {
        $accountType->delete();
        return response()->json(['message' => 'Account type deleted successfully']);
    }
}
