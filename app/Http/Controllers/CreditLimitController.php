<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditLimitStoreRequest;
use App\Http\Requests\CreditLimitUpdateRequest;
use App\Http\Resources\CreditLimitCollection;
use App\Http\Resources\CreditLimitResource;
use App\Models\CreditLimit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreditLimitController extends Controller
{
    public function index(Request $request): CreditLimitCollection
    {
        $creditLimits = CreditLimit::all();

        return new CreditLimitCollection($creditLimits);
    }

    public function store(CreditLimitStoreRequest $request): CreditLimitResource
    {
        $creditLimit = CreditLimit::create($request->validated());

        return new CreditLimitResource($creditLimit);
    }

    public function show(Request $request, CreditLimit $creditLimit): CreditLimitResource
    {
        return new CreditLimitResource($creditLimit);
    }

    public function update(CreditLimitUpdateRequest $request, CreditLimit $creditLimit): CreditLimitResource
    {
        $creditLimit->update($request->validated());

        return new CreditLimitResource($creditLimit);
    }

    public function destroy($id)
    {
       CreditLimit::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
