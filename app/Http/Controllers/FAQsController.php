<?php

namespace App\Http\Controllers;

use App\Http\Requests\FAQsStoreRequest;
use App\Http\Requests\FAQsUpdateRequest;
use App\Http\Resources\FAQCollection;
use App\Http\Resources\FAQResource;
use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FAQsController extends Controller
{
    public function index(Request $request): FAQCollection
    {
        $fAQS = FAQ::all();

        return new FAQCollection($fAQS);
    }

    public function store(FAQsStoreRequest $request): FAQResource
    {
        $fAQ = FAQ::create($request->validated());

        return new FAQResource($fAQ);
    }

    public function show(Request $request, FAQ $fAQ): FAQResource
    {
        return new FAQResource($fAQ);
    }

    public function update(FAQsUpdateRequest $request, FAQ $fAQ): FAQResource
    {
        $fAQ->update($request->validated());

        return new FAQResource($fAQ);
    }

    public function destroy(Request $request, FAQ $fAQ): Response
    {
        $fAQ->delete();

        return response()->noContent();
    }
}
