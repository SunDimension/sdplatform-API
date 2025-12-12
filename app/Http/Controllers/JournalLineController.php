<?php







namespace App\Http\Controllers;
use App\Http\Resources\JournalLineResource;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Http\Request;
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
}