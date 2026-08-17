<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Buyer;
use App\Models\Property;
use App\Models\PropertyOffer;
use App\Models\PropertyOffers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyOffersController
 */
final class PropertyOffersControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyOffers = PropertyOffers::factory()->count(3)->create();

        $response = $this->get(route('property-offers.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyOffersController::class,
            'store',
            \App\Http\Requests\PropertyOffersStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $buyer = Buyer::factory()->create();
        $offer_amount = $this->faker->randomFloat(/** float_attributes **/);
        $status = $this->faker->word();

        $response = $this->post(route('property-offers.store'), [
            'property_id' => $property->id,
            'buyer_id' => $buyer->id,
            'offer_amount' => $offer_amount,
            'status' => $status,
        ]);

        $propertyOffers = PropertyOffer::query()
            ->where('property_id', $property->id)
            ->where('buyer_id', $buyer->id)
            ->where('offer_amount', $offer_amount)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $propertyOffers);
        $propertyOffer = $propertyOffers->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyOffer = PropertyOffers::factory()->create();

        $response = $this->get(route('property-offers.show', $propertyOffer));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyOffersController::class,
            'update',
            \App\Http\Requests\PropertyOffersUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyOffer = PropertyOffers::factory()->create();
        $property = Property::factory()->create();
        $buyer = Buyer::factory()->create();
        $offer_amount = $this->faker->randomFloat(/** float_attributes **/);
        $status = $this->faker->word();

        $response = $this->put(route('property-offers.update', $propertyOffer), [
            'property_id' => $property->id,
            'buyer_id' => $buyer->id,
            'offer_amount' => $offer_amount,
            'status' => $status,
        ]);

        $propertyOffer->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $propertyOffer->property_id);
        $this->assertEquals($buyer->id, $propertyOffer->buyer_id);
        $this->assertEquals($offer_amount, $propertyOffer->offer_amount);
        $this->assertEquals($status, $propertyOffer->status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyOffer = PropertyOffers::factory()->create();
        $propertyOffer = PropertyOffer::factory()->create();

        $response = $this->delete(route('property-offers.destroy', $propertyOffer));

        $response->assertNoContent();

        $this->assertModelMissing($propertyOffer);
    }
}
