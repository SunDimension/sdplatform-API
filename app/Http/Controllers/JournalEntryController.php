<?php

namespace App\Http\Controllers;

use App\Http\Requests\JournalEntryStoreRequest;
use App\Http\Requests\JournalEntryUpdateRequest;
use App\Http\Resources\JournalEntryCollection;
use App\Http\Resources\JournalEntryResource;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $journalEntries = JournalEntry::all();

        return new JournalEntryCollection($journalEntries);
    }

    public function pending(Request $request)
    {
        $journalEntries = JournalEntry::where('approval_stage_id',1)->get();
        return new JournalEntryCollection($journalEntries);
    }

    public function store(JournalEntryStoreRequest $request)
    {
        $data = $request->validated();
        $journalEntry = JournalEntry::create($data);
        Log::debug($data);

        foreach ($data['journal_entries'] as $clauseEntry) {

            JournalEntryDetail::create([
                'journal_entry_id'=>$journalEntry->id,
                'journal_type_id' => $clauseEntry['journal_type_id'],
                'amount' => $clauseEntry['amount'],
                'account_id' => $clauseEntry['account_id'],
                'account_no' => $clauseEntry['account_no'],
                'description' => $clauseEntry['description'],
                 //'amount' => $journalEntry->id,
            ]);
        }

        return new JournalEntryResource($journalEntry);
    }

    public function show(Request $request, JournalEntry $journalEntry)
    {
        return new JournalEntryResource($journalEntry);
    }

    public function update(JournalEntryUpdateRequest $request, JournalEntry $journalEntry)
    {
        $journalEntry->update($request->validated());

        return new JournalEntryResource($journalEntry);
    }

    public function destroy(Request $request, JournalEntry $journalEntry)
    {
        $journalEntry->delete();

        return response()->noContent();
    }
}
