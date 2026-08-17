<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationsStoreRequest;
use App\Http\Requests\NotificationsUpdateRequest;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationsController extends Controller
{
    public function index(Request $request): NotificationCollection
    {
        $notifications = Notification::all();

        return new NotificationCollection($notifications);
    }

    public function store(NotificationsStoreRequest $request): NotificationResource
    {
        $notification = Notification::create($request->validated());

        return new NotificationResource($notification);
    }

    public function show(Request $request, Notification $notification): NotificationResource
    {
        return new NotificationResource($notification);
    }

    public function update(NotificationsUpdateRequest $request, Notification $notification): NotificationResource
    {
        $notification->update($request->validated());

        return new NotificationResource($notification);
    }

    public function destroy(Request $request, Notification $notification): Response
    {
        $notification->delete();

        return response()->noContent();
    }
}
