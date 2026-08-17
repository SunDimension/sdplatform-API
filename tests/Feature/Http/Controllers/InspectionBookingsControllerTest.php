<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\InspectionBooking;
use App\Models\InspectionBookings;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\InspectionBookingsController
 */
final class InspectionBookingsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $inspectionBookings = InspectionBookings::factory()->count(3)->create();

        $response = $this->get(route('inspection-bookings.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InspectionBookingsController::class,
            'store',
            \App\Http\Requests\InspectionBookingsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $user = User::factory()->create();
        $scheduled_at = Carbon::parse($this->faker->dateTime());
        $schedule_time = $this->faker->word();
        $status = $this->faker->word();

        $response = $this->post(route('inspection-bookings.store'), [
            'property_id' => $property->id,
            'user_id' => $user->id,
            'scheduled_at' => $scheduled_at->toDateTimeString(),
            'schedule_time' => $schedule_time,
            'status' => $status,
        ]);

        $inspectionBookings = InspectionBooking::query()
            ->where('property_id', $property->id)
            ->where('user_id', $user->id)
            ->where('scheduled_at', $scheduled_at)
            ->where('schedule_time', $schedule_time)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $inspectionBookings);
        $inspectionBooking = $inspectionBookings->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $inspectionBooking = InspectionBookings::factory()->create();

        $response = $this->get(route('inspection-bookings.show', $inspectionBooking));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InspectionBookingsController::class,
            'update',
            \App\Http\Requests\InspectionBookingsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $inspectionBooking = InspectionBookings::factory()->create();
        $property = Property::factory()->create();
        $user = User::factory()->create();
        $scheduled_at = Carbon::parse($this->faker->dateTime());
        $schedule_time = $this->faker->word();
        $status = $this->faker->word();

        $response = $this->put(route('inspection-bookings.update', $inspectionBooking), [
            'property_id' => $property->id,
            'user_id' => $user->id,
            'scheduled_at' => $scheduled_at->toDateTimeString(),
            'schedule_time' => $schedule_time,
            'status' => $status,
        ]);

        $inspectionBooking->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $inspectionBooking->property_id);
        $this->assertEquals($user->id, $inspectionBooking->user_id);
        $this->assertEquals($scheduled_at->timestamp, $inspectionBooking->scheduled_at);
        $this->assertEquals($schedule_time, $inspectionBooking->schedule_time);
        $this->assertEquals($status, $inspectionBooking->status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $inspectionBooking = InspectionBookings::factory()->create();
        $inspectionBooking = InspectionBooking::factory()->create();

        $response = $this->delete(route('inspection-bookings.destroy', $inspectionBooking));

        $response->assertNoContent();

        $this->assertModelMissing($inspectionBooking);
    }
}
