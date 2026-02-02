<?php

namespace App\Http\Controllers;
use App\Http\Resources\JournalLineResource;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JournalLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $journalLines = JournalLine::with('account')->get();
        return JournalLineResource::collection($journalLines);
    }

    public function searchJournalLines(Request $request)
    {
    
             $validated = $request->validate([
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
    ]);

        $query = JournalLine::query();

   

        if (!empty($validated['from_date']) && !empty($validated['to_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($validated['from_date'])->startOfDay(),
                Carbon::parse($validated['to_date'])->endOfDay(),
            ]);
        }

        return new AnonymousResourceCollection($query->orderBy('created_at', 'desc')->get());
    }
    }
