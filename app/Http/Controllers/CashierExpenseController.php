<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashierExpenseStoreRequest;
// use App\Http\Requests\BranchUpdateRequest;
use App\Http\Resources\CashierExpenseCollection;
use App\Http\Resources\CashierExpenseResource;
use App\Models\CashierExpense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class CashierExpenseController extends Controller
{
    public function index(Request $request): CashierExpenseCollection
    {
        $validated = $request->validate([
        'store_id' => 'nullable|integer|exists:stores,id',
        'branch_id' => 'nullable|integer|exists:stores,branch_id', // Ensure branch_id exists in stores
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date',
    ]);
         // Extract validated parameters
        $storeId = $validated['store_id'] ?? null;
        $branchId = $validated['branch_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        
        $query = CashierExpense::with(['store', 'user', 'branch', 'expense']);

          if ($storeId) {
        $query->where('store_id', $storeId);
    }

    // Apply branch filter if provided or based on the logged-in user
    if ($branchId) {
        $query->where('branch_id', $branchId);
    } else {
        
    }
    if ($fromDate || $toDate) {
        // Convert from_date and to_date to Carbon instances only if provided
        $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
        $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

        // Apply date filter for the selected range along with the branch condition
        if ($fromDate && $toDate) {
            // Both from_date and to_date are provided
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            // Only from_date is provided
            $query->where('created_at', '>=', $fromDate);
        } elseif ($toDate) {
            // Only to_date is provided
            $query->where('created_at', '<=', $toDate);
        }

        // Ensure transactions are fetched only for the user's branch when filtering by date
        $user = auth()->user(); // Get the logged-in user
        $query->where('branch_id', $user->branch_id); // Filter by branch_id (user's branch)
    }

    // Fetch the results
    $cashierRemit = $query->get();

    // Return the results as a collection
    

        return new CashierExpenseCollection($cashierRemit);
    }


    public function pending(Request $request): CashierExpenseCollection
    {
        $CashierExpense = CashierExpense::where('status','pending')->where('branch_id',auth()->user()->branch_id)->get();

        return new CashierExpenseCollection($CashierExpense);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id'=>['required']
        ]);

        
        $receiveOrder = CashierExpense::findOrFail($validated['id']);
        $receiveOrder->approval_comment = $validated['comment'];
        $receiveOrder->status = $validated['status'];
        $receiveOrder->approved_by = auth()->user()->id;
        $receiveOrder->approval_date = now();
        $receiveOrder->save();

        
        return new CashierExpenseResource($receiveOrder);
    }

    public function store(CashierExpenseStoreRequest $request): CashierExpenseResource
    {
        $CashierExpense = CashierExpense::create($request->validated());

        return new CashierExpenseResource($CashierExpense);
    }

    public function show(Request $request, CashierExpense $CashierExpense): CashierExpenseResource
    {
        return new CashierExpenseResource($CashierExpense);
    }

    // public function update(BranchUpdateRequest $request, Branch $branch): BranchResource
    // {
    //     $branch->update($request->validated());

    //     return new BranchResource($branch);
    // }

    public function destroy($id)
    {
        // $branch->delete();
        CashierExpense::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
