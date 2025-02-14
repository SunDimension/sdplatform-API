<?php

namespace App\Http\Controllers;

use App\Http\Requests\JournalEntryDetailStoreRequest;
use App\Http\Requests\JournalEntryDetailUpdateRequest;
use App\Http\Resources\JournalEntryDetailCollection;
use App\Http\Resources\JournalEntryDetailResource;
use App\Models\JournalEntryDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JournalEntryDetailController extends Controller
{
    public function index(Request $request)
    {
        $journalEntryDetails = JournalEntryDetail::all();

        return new JournalEntryDetailCollection($journalEntryDetails);
    }

    public function store(JournalEntryDetailStoreRequest $request)
    {
        $journalEntryDetail = JournalEntryDetail::create($request->validated());

        return new JournalEntryDetailResource($journalEntryDetail);
    }

    public function show(Request $request, JournalEntryDetail $journalEntryDetail)
    {
        return new JournalEntryDetailResource($journalEntryDetail);
    }

    public function update(JournalEntryDetailUpdateRequest $request, JournalEntryDetail $journalEntryDetail)
    {
        $journalEntryDetail->update($request->validated());

        return new JournalEntryDetailResource($journalEntryDetail);
    }

    public function destroy(Request $request, JournalEntryDetail $journalEntryDetail)
    {
        $journalEntryDetail->delete();

        return response()->noContent();
    }
}
