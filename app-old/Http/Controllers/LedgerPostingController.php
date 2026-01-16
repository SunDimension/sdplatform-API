<?php

namespace App\Http\Controllers;

use App\Http\Resources\LedgerPostingResource;
use App\Models\LedgerPosting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LedgerPostingController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $ledgerPostings = LedgerPosting::with('account')->get();

        return LedgerPostingResource::collection($ledgerPostings);
    }
}
