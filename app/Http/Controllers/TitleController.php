<?php

namespace App\Http\Controllers;

use App\Http\Requests\TitleStoreRequest;
use App\Http\Requests\TitleUpdateRequest;
use App\Http\Resources\TitleCollection;
use App\Http\Resources\TitleResource;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TitleController extends Controller
{
    public function index(Request $request): TitleCollection
    {
        $title = Title::all();

        return new TitleCollection($title);
    }

    public function store(TitleStoreRequest $request): TitleResource
    {
        $title = Title::create($request->validated());

        return new TitleResource($title);
    }

    public function show(Request $request, Title $title): TitleResource
    {
        return new TitleResource($title);
    }

    public function update(TitleUpdateRequest $request, Title $title): TitleResource
    {
        $title->update($request->validated());

        return new TitleResource($title);
    }

public function destroy($id)
    {   
       
        Title::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
