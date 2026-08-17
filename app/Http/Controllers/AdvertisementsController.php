<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdvertisementsStoreRequest;
use App\Http\Requests\AdvertisementsUpdateRequest;
use App\Http\Resources\AdvertisementCollection;
use App\Http\Resources\AdvertisementResource;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdvertisementsController extends Controller
{
    public function index(Request $request): AdvertisementCollection
    {
        $advertisements = Advertisement::all();

        return new AdvertisementCollection($advertisements);
    }

    public function store(AdvertisementsStoreRequest $request): AdvertisementResource
    {
        $advertisement = Advertisement::create($request->validated());

        return new AdvertisementResource($advertisement);
    }

    public function show(Request $request, Advertisement $advertisement): AdvertisementResource
    {
        return new AdvertisementResource($advertisement);
    }

    public function update(AdvertisementsUpdateRequest $request, Advertisement $advertisement): AdvertisementResource
    {
        $advertisement->update($request->validated());

        return new AdvertisementResource($advertisement);
    }

    public function destroy(Request $request, Advertisement $advertisement): Response
    {
        $advertisement->delete();

        return response()->noContent();
    }
}
