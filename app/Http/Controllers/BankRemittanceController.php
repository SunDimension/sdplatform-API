<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRemittanceStoreRequest;
use App\Http\Resources\BankRemittanceCollection;
use App\Http\Resources\BankRemittanceResource;
use App\Models\BankRemittance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BankRemittanceController extends Controller
{
public function index(Request $request): BankRemittanceCollection
    {
        $validated = $request->validate([
        'bank_id' => 'nullable|integer|exists:banks,id',
        'store_id' => 'nullable|string|exists:stores,id',
        'branch_id' => 'nullable|string|exists:stores,branch_id', // Ensure branch_id exists in stores
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date',
    ]);
         // Extract validated parameters
        $bankId = $validated['bank_id'] ?? null;
         $storeId = $validated['store_id'] ?? null;
        $branchId = $validated['branch_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        
        $query = BankRemittance::with(['store', 'user', 'branch', 'bank']);
            $query->where('status', 'approved');
          if ($bankId) {
        $query->where('bank_id', $bankId);
    }
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
    $bankRemit = $query->get();

    // Return the results as a collection
    

        return new BankRemittanceCollection($bankRemit);
    }
    public function pending(Request $request): BankRemittanceCollection
    {
        $user = auth()->user();
        Log::info('Fetching pending bank remittances for branch.', ['branch_id' => $user->branch_id, 'user_id' => $user->id]);

        $bankRemit = BankRemittance::where('status', 'pending')
            ->where('branch_id', $user->branch_id)
            ->get();

        Log::info('Fetched pending bank remittances:', ['count' => $bankRemit->count()]);

        return new BankRemittanceCollection($bankRemit);
    }

    public function approve(Request $request)
    {
        Log::info('Approving bank remittance.', ['request_data' => $request->all()]);

        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id' => ['required']
        ]);

        $receiveOrder = BankRemittance::findOrFail($validated['id']);
        Log::info('Found bank remittance to approve.', ['id' => $validated['id']]);

        $receiveOrder->approval_comment = $validated['comment'];
        $receiveOrder->status = $validated['status'];
        $receiveOrder->approved_by = auth()->user()->id;
        $receiveOrder->approval_date = now();
        $receiveOrder->save();

        Log::info('Bank remittance approved.', ['id' => $validated['id'], 'status' => $validated['status']]);

        return new BankRemittanceResource($receiveOrder);
    }

    public function store(BankRemittanceStoreRequest $request): BankRemittanceResource
    {
        Log::info('Storing new bank remittance.', ['request_data' => $request->validated()]);

        $bankRemit = BankRemittance::create($request->validated());

        Log::info('New bank remittance created.', ['id' => $bankRemit->id]);

        return new BankRemittanceResource($bankRemit);
    }

    public function show(Request $request, BankRemittance $bankRemit): BankRemittanceResource
    {
        Log::info('Fetching bank remittance details.', ['id' => $bankRemit->id]);

        return new BankRemittanceResource($bankRemit);
    }

    public function get($id): BankRemittanceResource
    { 
        $cashierRemit = BankRemittance::findOrFail($id);
        return new BankRemittanceResource($cashierRemit);
    }


    public function destroy($id)
    {
        Log::info('Deleting bank remittance.', ['id' => $id]);

        $result = BankRemittance::destroy($id);

        if ($result) {
            Log::info('Bank remittance deleted successfully.', ['id' => $id]);
        } else {
            Log::warning('Failed to delete bank remittance.', ['id' => $id]);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
