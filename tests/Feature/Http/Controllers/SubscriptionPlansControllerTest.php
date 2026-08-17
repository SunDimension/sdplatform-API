<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlans;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SubscriptionPlansController
 */
final class SubscriptionPlansControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $subscriptionPlans = SubscriptionPlans::factory()->count(3)->create();

        $response = $this->get(route('subscription-plans.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SubscriptionPlansController::class,
            'store',
            \App\Http\Requests\SubscriptionPlansStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = $this->faker->name();
        $price = $this->faker->randomFloat(/** float_attributes **/);
        $duration = $this->faker->numberBetween(-10000, 10000);
        $listing_limit = $this->faker->numberBetween(-10000, 10000);
        $featured_limit = $this->faker->numberBetween(-10000, 10000);
        $priority_support = $this->faker->boolean();

        $response = $this->post(route('subscription-plans.store'), [
            'name' => $name,
            'price' => $price,
            'duration' => $duration,
            'listing_limit' => $listing_limit,
            'featured_limit' => $featured_limit,
            'priority_support' => $priority_support,
        ]);

        $subscriptionPlans = SubscriptionPlan::query()
            ->where('name', $name)
            ->where('price', $price)
            ->where('duration', $duration)
            ->where('listing_limit', $listing_limit)
            ->where('featured_limit', $featured_limit)
            ->where('priority_support', $priority_support)
            ->get();
        $this->assertCount(1, $subscriptionPlans);
        $subscriptionPlan = $subscriptionPlans->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $subscriptionPlan = SubscriptionPlans::factory()->create();

        $response = $this->get(route('subscription-plans.show', $subscriptionPlan));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SubscriptionPlansController::class,
            'update',
            \App\Http\Requests\SubscriptionPlansUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $subscriptionPlan = SubscriptionPlans::factory()->create();
        $name = $this->faker->name();
        $price = $this->faker->randomFloat(/** float_attributes **/);
        $duration = $this->faker->numberBetween(-10000, 10000);
        $listing_limit = $this->faker->numberBetween(-10000, 10000);
        $featured_limit = $this->faker->numberBetween(-10000, 10000);
        $priority_support = $this->faker->boolean();

        $response = $this->put(route('subscription-plans.update', $subscriptionPlan), [
            'name' => $name,
            'price' => $price,
            'duration' => $duration,
            'listing_limit' => $listing_limit,
            'featured_limit' => $featured_limit,
            'priority_support' => $priority_support,
        ]);

        $subscriptionPlan->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $subscriptionPlan->name);
        $this->assertEquals($price, $subscriptionPlan->price);
        $this->assertEquals($duration, $subscriptionPlan->duration);
        $this->assertEquals($listing_limit, $subscriptionPlan->listing_limit);
        $this->assertEquals($featured_limit, $subscriptionPlan->featured_limit);
        $this->assertEquals($priority_support, $subscriptionPlan->priority_support);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $subscriptionPlan = SubscriptionPlans::factory()->create();
        $subscriptionPlan = SubscriptionPlan::factory()->create();

        $response = $this->delete(route('subscription-plans.destroy', $subscriptionPlan));

        $response->assertNoContent();

        $this->assertModelMissing($subscriptionPlan);
    }
}
