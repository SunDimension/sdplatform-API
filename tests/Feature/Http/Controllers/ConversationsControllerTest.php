<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Buyer;
use App\Models\Conversation;
use App\Models\Conversations;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ConversationsController
 */
final class ConversationsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $conversations = Conversations::factory()->count(3)->create();

        $response = $this->get(route('conversations.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ConversationsController::class,
            'store',
            \App\Http\Requests\ConversationsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $buyer = Buyer::factory()->create();
        $seller = Seller::factory()->create();

        $response = $this->post(route('conversations.store'), [
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $conversations = Conversation::query()
            ->where('buyer_id', $buyer->id)
            ->where('seller_id', $seller->id)
            ->get();
        $this->assertCount(1, $conversations);
        $conversation = $conversations->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $conversation = Conversations::factory()->create();

        $response = $this->get(route('conversations.show', $conversation));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ConversationsController::class,
            'update',
            \App\Http\Requests\ConversationsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $conversation = Conversations::factory()->create();
        $buyer = Buyer::factory()->create();
        $seller = Seller::factory()->create();

        $response = $this->put(route('conversations.update', $conversation), [
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $conversation->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($buyer->id, $conversation->buyer_id);
        $this->assertEquals($seller->id, $conversation->seller_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $conversation = Conversations::factory()->create();
        $conversation = Conversation::factory()->create();

        $response = $this->delete(route('conversations.destroy', $conversation));

        $response->assertNoContent();

        $this->assertModelMissing($conversation);
    }
}
