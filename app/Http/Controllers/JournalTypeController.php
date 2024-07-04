<?php

namespace App\Http\Controllers;

use App\Http\Requests\JournalTypeStoreRequest;
use App\Http\Requests\JournalTypeUpdateRequest;
use App\Http\Resources\JournalTypeCollection;
use App\Http\Resources\JournalTypeResource;
use App\Models\JournalType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JournalTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $journalTypes = JournalType::all();

        return new JournalTypeCollection($journalTypes);
    }

    public function store(JournalTypeStoreRequest $request): Response
    {
        $journalType = JournalType::create($request->validated());

        return new JournalTypeResource($journalType);
    }

    public function show(Request $request, JournalType $journalType): Response
    {
        return new JournalTypeResource($journalType);
    }

    public function update(JournalTypeUpdateRequest $request, JournalType $journalType): Response
    {
        $journalType->update($request->validated());

        return new JournalTypeResource($journalType);
    }

    public function destroy(Request $request, JournalType $journalType): Response
    {
        $journalType->delete();

        return response()->noContent();
    }
}
