<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversationsStoreRequest;
use App\Http\Requests\ConversationsUpdateRequest;
use App\Http\Resources\ConversationCollection;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConversationsController extends Controller
{
    public function index(Request $request): ConversationCollection
    {
        $conversations = Conversation::all();

        return new ConversationCollection($conversations);
    }

    public function store(ConversationsStoreRequest $request): ConversationResource
    {
        $conversation = Conversation::create($request->validated());

        return new ConversationResource($conversation);
    }

    public function show(Request $request, Conversation $conversation): ConversationResource
    {
        return new ConversationResource($conversation);
    }

    public function update(ConversationsUpdateRequest $request, Conversation $conversation): ConversationResource
    {
        $conversation->update($request->validated());

        return new ConversationResource($conversation);
    }

    public function destroy(Request $request, Conversation $conversation): Response
    {
        $conversation->delete();

        return response()->noContent();
    }
}
