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
    public function index(Request $request): Response
    {
        $journalEntryDetails = JournalEntryDetail::all();

        return new JournalEntryDetailCollection($journalEntryDetails);
    }

    public function store(JournalEntryDetailStoreRequest $request): Response
    {
        $journalEntryDetail = JournalEntryDetail::create($request->validated());

        return new JournalEntryDetailResource($journalEntryDetail);
    }

    public function show(Request $request, JournalEntryDetail $journalEntryDetail): Response
    {
        return new JournalEntryDetailResource($journalEntryDetail);
    }

    public function update(JournalEntryDetailUpdateRequest $request, JournalEntryDetail $journalEntryDetail): Response
    {
        $journalEntryDetail->update($request->validated());

        return new JournalEntryDetailResource($journalEntryDetail);
    }

    public function destroy(Request $request, JournalEntryDetail $journalEntryDetail): Response
    {
        $journalEntryDetail->delete();

        return response()->noContent();
    }
}
