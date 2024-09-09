<?php

namespace App\Http\Controllers;

use App\Http\Requests\JournalEntryStoreRequest;
use App\Http\Requests\JournalEntryUpdateRequest;
use App\Http\Resources\JournalEntryCollection;
use App\Http\Resources\JournalEntryResource;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $journalEntries = JournalEntry::all();

        return new JournalEntryCollection($journalEntries);
    }

    public function store(JournalEntryStoreRequest $request)
    {
        $journalEntry = JournalEntry::create($request->validated());

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
