<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardSettingStoreRequest;
use App\Http\Requests\DashboardSettingUpdateRequest;
use App\Http\Resources\DashboardSettingCollection;
use App\Http\Resources\DashboardSettingResource;
use App\Models\DashboardSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardSettingController extends Controller
{
    public function index(Request $request): Response
    {
        $dashboardSettings = DashboardSetting::all();

        return new DashboardSettingCollection($dashboardSettings);
    }

    public function store(DashboardSettingStoreRequest $request): Response
    {
        $dashboardSetting = DashboardSetting::create($request->validated());

        return new DashboardSettingResource($dashboardSetting);
    }

    public function show(Request $request, DashboardSetting $dashboardSetting): Response
    {
        return new DashboardSettingResource($dashboardSetting);
    }

    public function update(DashboardSettingUpdateRequest $request, DashboardSetting $dashboardSetting): Response
    {
        $dashboardSetting->update($request->validated());

        return new DashboardSettingResource($dashboardSetting);
    }

    public function destroy(Request $request, DashboardSetting $dashboardSetting): Response
    {
        $dashboardSetting->delete();

        return response()->noContent();
    }
}
