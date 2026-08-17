<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionsStoreRequest;
use App\Http\Requests\SubscriptionsUpdateRequest;
use App\Http\Resources\SubscriptionCollection;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionsController extends Controller
{
    public function index(Request $request): SubscriptionCollection
    {
        $subscriptions = Subscription::all();

        return new SubscriptionCollection($subscriptions);
    }

    public function store(SubscriptionsStoreRequest $request): SubscriptionResource
    {
        $subscription = Subscription::create($request->validated());

        return new SubscriptionResource($subscription);
    }

    public function show(Request $request, Subscription $subscription): SubscriptionResource
    {
        return new SubscriptionResource($subscription);
    }

    public function update(SubscriptionsUpdateRequest $request, Subscription $subscription): SubscriptionResource
    {
        $subscription->update($request->validated());

        return new SubscriptionResource($subscription);
    }

    public function destroy(Request $request, Subscription $subscription): Response
    {
        $subscription->delete();

        return response()->noContent();
    }
}
