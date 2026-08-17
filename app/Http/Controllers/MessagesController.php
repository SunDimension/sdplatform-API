<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessagesStoreRequest;
use App\Http\Requests\MessagesUpdateRequest;
use App\Http\Resources\MessageCollection;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessagesController extends Controller
{
    public function index(Request $request): MessageCollection
    {
        $messages = Message::all();

        return new MessageCollection($messages);
    }

    public function store(MessagesStoreRequest $request): MessageResource
    {
        $message = Message::create($request->validated());

        return new MessageResource($message);
    }

    public function show(Request $request, Message $message): MessageResource
    {
        return new MessageResource($message);
    }

    public function update(MessagesUpdateRequest $request, Message $message): MessageResource
    {
        $message->update($request->validated());

        return new MessageResource($message);
    }

    public function destroy(Request $request, Message $message): Response
    {
        $message->delete();

        return response()->noContent();
    }
}
