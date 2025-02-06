<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\YearStoreRequest;
use App\Http\Requests\YearUpdateRequest;
use App\Http\Resources\YearCollection;
use App\Http\Resources\YearResource;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YearController extends Controller
{
    
    public function index(Request $request): YearCollection
    {
        $years = Year::all();

        return new YearCollection($years);
    }

    public function store(YearStoreRequest $request): YearResource
    {
        $years = Year::create($request->validated());

        return new YearResource($years);
    }

    public function show(Request $request, Year $years): YearResource
    {
        return new YearResource($years);
    }

    public function update(YearUpdateRequest $request, Year $years): YearResource
    {
        $years->update($request->validated());

        return new YearResource($years);
    }

  public function destroy($id)
    {   
       
        Year::destroy($id);

        
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
