<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseLineStoreRequest;
use App\Http\Requests\ExpenseLineUpdateRequest;
use App\Http\Resources\ExpenseLineCollection;
use App\Http\Resources\ExpenseLineResource;
use App\Models\ExpenseLine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExpenseLineController extends Controller
{
    public function index(Request $request): ExpenseLineCollection
    {
        $expense_line = ExpenseLine::all();

        return new ExpenseLineCollection($expense_line);
    }

    public function store(ExpenseLineStoreRequest $request): ExpenseLineResource
    {
        $expense_line = ExpenseLine::create($request->validated());

        return new ExpenseLineResource($expense_line);
    }

    public function show(Request $request, ExpenseLine $expense_line): ExpenseLineResource
    {
        return new ExpenseLineResource($expense_line);
    }

    public function update(ExpenseLineUpdateRequest $request, ExpenseLine $expense_line): ExpenseLineResource
    {
        $expense_line->update($request->validated());

        return new ExpenseLineResource($expense_line);
    }

  public function destroy($id)
    {   
       
        ExpenseLine::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
