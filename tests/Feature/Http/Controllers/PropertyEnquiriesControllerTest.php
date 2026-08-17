<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyEnquiries;
use App\Models\PropertyEnquiry;
use App\Models\Receiver;
use App\Models\Sender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyEnquiriesController
 */
final class PropertyEnquiriesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyEnquiries = PropertyEnquiries::factory()->count(3)->create();

        $response = $this->get(route('property-enquiries.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyEnquiriesController::class,
            'store',
            \App\Http\Requests\PropertyEnquiriesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $sender = Sender::factory()->create();
        $receiver = Receiver::factory()->create();
        $message = $this->faker->text();
        $status = $this->faker->word();

        $response = $this->post(route('property-enquiries.store'), [
            'property_id' => $property->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $message,
            'status' => $status,
        ]);

        $propertyEnquiries = PropertyEnquiry::query()
            ->where('property_id', $property->id)
            ->where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->where('message', $message)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $propertyEnquiries);
        $propertyEnquiry = $propertyEnquiries->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyEnquiry = PropertyEnquiries::factory()->create();

        $response = $this->get(route('property-enquiries.show', $propertyEnquiry));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyEnquiriesController::class,
            'update',
            \App\Http\Requests\PropertyEnquiriesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyEnquiry = PropertyEnquiries::factory()->create();
        $property = Property::factory()->create();
        $sender = Sender::factory()->create();
        $receiver = Receiver::factory()->create();
        $message = $this->faker->text();
        $status = $this->faker->word();

        $response = $this->put(route('property-enquiries.update', $propertyEnquiry), [
            'property_id' => $property->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $message,
            'status' => $status,
        ]);

        $propertyEnquiry->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $propertyEnquiry->property_id);
        $this->assertEquals($sender->id, $propertyEnquiry->sender_id);
        $this->assertEquals($receiver->id, $propertyEnquiry->receiver_id);
        $this->assertEquals($message, $propertyEnquiry->message);
        $this->assertEquals($status, $propertyEnquiry->status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyEnquiry = PropertyEnquiries::factory()->create();
        $propertyEnquiry = PropertyEnquiry::factory()->create();

        $response = $this->delete(route('property-enquiries.destroy', $propertyEnquiry));

        $response->assertNoContent();

        $this->assertModelMissing($propertyEnquiry);
    }
}
