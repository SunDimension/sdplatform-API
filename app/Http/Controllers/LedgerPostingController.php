<?php

namespace App\Http\Controllers;

use App\Http\Resources\LedgerPostingResource;
use App\Models\LedgerPosting;
use App\Http\Resources\LedgerPostingCollection;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LedgerPostingController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $ledgerPostings = LedgerPosting::with('account')->get();

        return LedgerPostingResource::collection($ledgerPostings);
    }

    public function SearchLedgerPosting(Request $request)
       {
      $validated = $request->validate([
        'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
    ]);

        $query = LedgerPosting::query();



        if (!empty($validated['from_date']) && !empty($validated['to_date'])) {
            $query->whereBetween('posting_date', [
                Carbon::parse($validated['from_date'])->startOfDay(),
                Carbon::parse($validated['to_date'])->endOfDay(),
            ]);
        }

        return new LedgerPostingCollection($query->orderBy('posting_date', 'desc')->get());
    }
}
