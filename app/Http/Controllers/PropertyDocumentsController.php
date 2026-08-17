<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyDocumentsStoreRequest;
use App\Http\Requests\PropertyDocumentsUpdateRequest;
use App\Http\Resources\PropertyDocumentCollection;
use App\Http\Resources\PropertyDocumentResource;
use App\Models\PropertyDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyDocumentsController extends Controller
{
    public function index(Request $request): PropertyDocumentCollection
    {
        $propertyDocuments = PropertyDocument::all();

        return new PropertyDocumentCollection($propertyDocuments);
    }

    public function store(PropertyDocumentsStoreRequest $request): PropertyDocumentResource
    {
        $propertyDocument = PropertyDocument::create($request->validated());

        return new PropertyDocumentResource($propertyDocument);
    }

    public function show(Request $request, PropertyDocument $propertyDocument): PropertyDocumentResource
    {
        return new PropertyDocumentResource($propertyDocument);
    }

    public function update(PropertyDocumentsUpdateRequest $request, PropertyDocument $propertyDocument): PropertyDocumentResource
    {
        $propertyDocument->update($request->validated());

        return new PropertyDocumentResource($propertyDocument);
    }

    public function destroy(Request $request, PropertyDocument $propertyDocument): Response
    {
        $propertyDocument->delete();

        return response()->noContent();
    }
}
