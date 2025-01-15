<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRemittanceStoreRequest;
use App\Http\Resources\BankRemittanceCollection;
use App\Http\Resources\BankRemittanceResource;
use App\Models\BankRemittance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class BankRemittanceController extends Controller
{
    public function index(Request $request): BankRemittanceCollection
    {
        Log::info('Fetching all bank remittances.');

        $bankRemit = BankRemittance::all();

        Log::info('Fetched bank remittances:', ['count' => $bankRemit->count()]);

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
