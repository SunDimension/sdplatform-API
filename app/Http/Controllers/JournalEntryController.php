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
    public function index(Request $request): Response
    {
        $journalEntries = JournalEntry::all();

        return new JournalEntryCollection($journalEntries);
    }

    public function store(JournalEntryStoreRequest $request): Response
    {
        $journalEntry = JournalEntry::create($request->validated());

        return new JournalEntryResource($journalEntry);
    }

    public function show(Request $request, JournalEntry $journalEntry): Response
    {
        return new JournalEntryResource($journalEntry);
    }

    public function update(JournalEntryUpdateRequest $request, JournalEntry $journalEntry): Response
    {
        $journalEntry->update($request->validated());

        return new JournalEntryResource($journalEntry);
    }

    public function destroy(Request $request, JournalEntry $journalEntry): Response
    {
        $journalEntry->delete();

        return response()->noContent();
    }
}
