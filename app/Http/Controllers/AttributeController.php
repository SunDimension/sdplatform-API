<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeStoreRequest;
use App\Http\Requests\AttributeUpdateRequest;
use App\Http\Resources\AttributeCollection;
use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AttributeController extends Controller
{
    public function index(Request $request): AttributeCollection
    {
        $attributes = Attribute::all();

        return new AttributeCollection($attributes);
    }

    public function store(AttributeStoreRequest $request): AttributeResource
    {
        $attribute = Attribute::create($request->validated());

        return new AttributeResource($attribute);
    }

    public function show(Request $request, Attribute $attribute): AttributeResource
    {
        return new AttributeResource($attribute);
    }

    public function update(AttributeUpdateRequest $request, Attribute $attribute): AttributeResource
    {
        $attribute->update($request->validated());

        return new AttributeResource($attribute);
    }

    public function destroy($id): Response
    {
        Attribute::destroy($id);

        return response()->noContent();
    }
}
