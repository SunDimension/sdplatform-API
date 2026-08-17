<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionPlansStoreRequest;
use App\Http\Requests\SubscriptionPlansUpdateRequest;
use App\Http\Resources\SubscriptionPlanCollection;
use App\Http\Resources\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionPlansController extends Controller
{
    public function index(Request $request): SubscriptionPlanCollection
    {
        $subscriptionPlans = SubscriptionPlan::all();

        return new SubscriptionPlanCollection($subscriptionPlans);
    }

    public function store(SubscriptionPlansStoreRequest $request): SubscriptionPlanResource
    {
        $subscriptionPlan = SubscriptionPlan::create($request->validated());

        return new SubscriptionPlanResource($subscriptionPlan);
    }

    public function show(Request $request, SubscriptionPlan $subscriptionPlan): SubscriptionPlanResource
    {
        return new SubscriptionPlanResource($subscriptionPlan);
    }

    public function update(SubscriptionPlansUpdateRequest $request, SubscriptionPlan $subscriptionPlan): SubscriptionPlanResource
    {
        $subscriptionPlan->update($request->validated());

        return new SubscriptionPlanResource($subscriptionPlan);
    }

    public function destroy(Request $request, SubscriptionPlan $subscriptionPlan): Response
    {
        $subscriptionPlan->delete();

        return response()->noContent();
    }
}
