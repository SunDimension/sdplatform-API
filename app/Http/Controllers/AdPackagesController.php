<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdPackagesStoreRequest;
use App\Http\Requests\AdPackagesUpdateRequest;
use App\Http\Resources\AdPackageCollection;
use App\Http\Resources\AdPackageResource;
use App\Models\AdPackage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdPackagesController extends Controller
{
    public function index(Request $request): AdPackageCollection
    {
        $adPackages = AdPackage::all();

        return new AdPackageCollection($adPackages);
    }

    public function store(AdPackagesStoreRequest $request): AdPackageResource
    {
        $adPackage = AdPackage::create($request->validated());

        return new AdPackageResource($adPackage);
    }

    public function show(Request $request, AdPackage $adPackage): AdPackageResource
    {
        return new AdPackageResource($adPackage);
    }

    public function update(AdPackagesUpdateRequest $request, AdPackage $adPackage): AdPackageResource
    {
        $adPackage->update($request->validated());

        return new AdPackageResource($adPackage);
    }

    public function destroy(Request $request, AdPackage $adPackage): Response
    {
        $adPackage->delete();

        return response()->noContent();
    }
}
