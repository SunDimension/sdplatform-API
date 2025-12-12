<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashierExpenseStoreRequest;
use App\Http\Resources\CashierExpenseCollection;
use App\Http\Resources\CashierExpenseResource;
use App\Models\CashierExpense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CashierExpenseController extends Controller
{
    public function index(Request $request): CashierExpenseCollection
    {
        $validated = $request->validate([
            'store_id' => 'nullable|string|exists:stores,id',
            'branch_id' => 'nullable|string|exists:stores,branch_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $storeId = $validated['store_id'] ?? null;
        $branchId = $validated['branch_id'] ?? null;
        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        $query = CashierExpense::with(['store', 'user', 'branch', 'expense']);
        $query->where('status', 'approved');

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($fromDate || $toDate) {
            $fromDate = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
            $toDate = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

            if ($fromDate && $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->where('created_at', '>=', $fromDate);
            } elseif ($toDate) {
                $query->where('created_at', '<=', $toDate);
            }

            $user = auth()->user();
            $query->where('branch_id', $user->branch_id);
        }

        $cashierRemit = $query->get();

        return new CashierExpenseCollection($cashierRemit);
    }

    public function pending(Request $request): CashierExpenseCollection
    {
        $CashierExpense = CashierExpense::where('status', 'pending')
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        return new CashierExpenseCollection($CashierExpense);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id' => ['required']
        ]);

        DB::beginTransaction();

        try {
            $expense = CashierExpense::findOrFail($validated['id']);

            // Update approval details
            $expense->approval_comment = $validated['comment'];
            $expense->status = $validated['status'];
            $expense->approved_by = auth()->user()->id;
            $expense->approval_date = now();
            $expense->save();

            // ======================================================
            // POST ACCOUNTING ENTRIES AFTER APPROVAL
            // ======================================================
            if ($validated['status'] === 'Approved') {
                $this->postExpenseAccountingEntries(
                    $expense->id,
                    $expense->account_id,
                    $expense->amount,
                    $expense->payment_method,
                    $expense->date ?? $expense->created_at,
                    $expense->narration
                );
            }

            DB::commit();

            return new CashierExpenseResource($expense);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Expense Approval Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'expense_id' => $validated['id']
            ]);

            return response()->json([
                'error' => 'An error occurred while approving the expense.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
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

    public function destroy($id)
    {
        CashierExpense::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Post accounting entries for approved expense
     */
    private function postExpenseAccountingEntries(
        string $expenseId,
        string $expenseAccountId,
        float $amount,
        string $paymentMethod,
        $expenseDate,
        ?string $narration = null
    ) {
        // Get Cash or Bank account based on payment method
        $paymentAccountCode = match ($paymentMethod) {
            'Cash' => '1000',  // Cash Account
            'Bank' => '1010',  // Bank Account
            default => throw new \Exception("Invalid payment method: {$paymentMethod}")
        };

        $paymentAccount = DB::table('ledger_accounts')
            ->where('account_code', $paymentAccountCode)
            ->value('account_id');

        // Validate accounts exist
        if (!$paymentAccount) {
            throw new \Exception("Payment account ({$paymentMethod}) not found in chart of accounts");
        }

        // Verify expense account exists
        $expenseAccountExists = DB::table('ledger_accounts')
            ->where('account_id', $expenseAccountId)
            ->exists();

        if (!$expenseAccountExists) {
            throw new \Exception("Expense account not found in chart of accounts");
        }

        $transactionId = Str::uuid()->toString();
        $journalId = Str::uuid()->toString();

        $description = $narration
            ? "Expense #{$expenseId} - {$narration}"
            : "{$paymentMethod} Expense #{$expenseId}";

        // 1. Create Transaction Record
        DB::table('transactions')->insert([
            'transaction_id' => $transactionId,
            'transaction_type' => 'EXPENSE',
            'reference_id' => $expenseId,
            'transaction_date' => date('Y-m-d', strtotime($expenseDate)),
            'total_amount' => $amount,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create Journal Entry Header
        DB::table('journal_entry')->insert([
            'journal_id' => $journalId,
            'transaction_id' => $transactionId,
            'entry_date' => date('Y-m-d', strtotime($expenseDate)),
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create Journal Lines (Double Entry)

        // DEBIT: Expense Account
        DB::table('journal_lines')->insert([
            'line_id' => Str::uuid()->toString(),
            'journal_id' => $journalId,
            'account_id' => $expenseAccountId,
            'debit_amount' => $amount,
            'credit_amount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // CREDIT: Cash or Bank Account
        DB::table('journal_lines')->insert([
            'line_id' => Str::uuid()->toString(),
            'journal_id' => $journalId,
            'account_id' => $paymentAccount,
            'debit_amount' => 0,
            'credit_amount' => $amount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Post to General Ledger
        $journalLines = DB::table('journal_lines')
            ->where('journal_id', $journalId)
            ->get();

        foreach ($journalLines as $line) {
            DB::table('ledger_postings')->insert([
                'posting_id' => Str::uuid()->toString(),
                'line_id' => $line->line_id,
                'account_id' => $line->account_id,
                'posting_date' => date('Y-m-d', strtotime($expenseDate)),
                'debit_amount' => $line->debit_amount,
                'credit_amount' => $line->credit_amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
