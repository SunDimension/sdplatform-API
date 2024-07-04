<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartCardStoreRequest;
use App\Http\Requests\ChartCardUpdateRequest;
use App\Http\Resources\ChartCardCollection;
use App\Http\Resources\ChartCardResource;
use App\Models\ChartCard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChartCardController extends Controller
{
    public function index(Request $request): Response
    {
        $chartCards = ChartCard::all();

        return new ChartCardCollection($chartCards);
    }

    public function store(ChartCardStoreRequest $request): Response
    {
        $chartCard = ChartCard::create($request->validated());

        return new ChartCardResource($chartCard);
    }

    public function show(Request $request, ChartCard $chartCard): Response
    {
        return new ChartCardResource($chartCard);
    }

    public function update(ChartCardUpdateRequest $request, ChartCard $chartCard): Response
    {
        $chartCard->update($request->validated());

        return new ChartCardResource($chartCard);
    }

    public function destroy(Request $request, ChartCard $chartCard): Response
    {
        $chartCard->delete();

        return response()->noContent();
    }
}
