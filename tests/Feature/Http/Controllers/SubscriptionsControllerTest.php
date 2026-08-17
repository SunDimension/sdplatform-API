<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Subscriptions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SubscriptionsController
 */
final class SubscriptionsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $subscriptions = Subscriptions::factory()->count(3)->create();

        $response = $this->get(route('subscriptions.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SubscriptionsController::class,
            'store',
            \App\Http\Requests\SubscriptionsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $user = User::factory()->create();
        $subscription_plan = SubscriptionPlan::factory()->create();
        $start_date = Carbon::parse($this->faker->date());
        $end_date = Carbon::parse($this->faker->date());

        $response = $this->post(route('subscriptions.store'), [
            'user_id' => $user->id,
            'subscription_plan_id' => $subscription_plan->id,
            'start_date' => $start_date->toDateString(),
            'end_date' => $end_date->toDateString(),
        ]);

        $subscriptions = Subscription::query()
            ->where('user_id', $user->id)
            ->where('subscription_plan_id', $subscription_plan->id)
            ->where('start_date', $start_date)
            ->where('end_date', $end_date)
            ->get();
        $this->assertCount(1, $subscriptions);
        $subscription = $subscriptions->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $subscription = Subscriptions::factory()->create();

        $response = $this->get(route('subscriptions.show', $subscription));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SubscriptionsController::class,
            'update',
            \App\Http\Requests\SubscriptionsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $subscription = Subscriptions::factory()->create();
        $user = User::factory()->create();
        $subscription_plan = SubscriptionPlan::factory()->create();
        $start_date = Carbon::parse($this->faker->date());
        $end_date = Carbon::parse($this->faker->date());

        $response = $this->put(route('subscriptions.update', $subscription), [
            'user_id' => $user->id,
            'subscription_plan_id' => $subscription_plan->id,
            'start_date' => $start_date->toDateString(),
            'end_date' => $end_date->toDateString(),
        ]);

        $subscription->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($user->id, $subscription->user_id);
        $this->assertEquals($subscription_plan->id, $subscription->subscription_plan_id);
        $this->assertEquals($start_date, $subscription->start_date);
        $this->assertEquals($end_date, $subscription->end_date);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $subscription = Subscriptions::factory()->create();
        $subscription = Subscription::factory()->create();

        $response = $this->delete(route('subscriptions.destroy', $subscription));

        $response->assertNoContent();

        $this->assertModelMissing($subscription);
    }
}
