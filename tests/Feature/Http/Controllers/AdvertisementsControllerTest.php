<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AdPackage;
use App\Models\Advertisement;
use App\Models\Advertisements;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AdvertisementsController
 */
final class AdvertisementsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $advertisements = Advertisements::factory()->count(3)->create();

        $response = $this->get(route('advertisements.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AdvertisementsController::class,
            'store',
            \App\Http\Requests\AdvertisementsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $user = User::factory()->create();
        $ad_package = AdPackage::factory()->create();
        $property = Property::factory()->create();
        $start_date = Carbon::parse($this->faker->date());
        $end_date = Carbon::parse($this->faker->date());
        $status = $this->faker->word();

        $response = $this->post(route('advertisements.store'), [
            'user_id' => $user->id,
            'ad_package_id' => $ad_package->id,
            'property_id' => $property->id,
            'start_date' => $start_date->toDateString(),
            'end_date' => $end_date->toDateString(),
            'status' => $status,
        ]);

        $advertisements = Advertisement::query()
            ->where('user_id', $user->id)
            ->where('ad_package_id', $ad_package->id)
            ->where('property_id', $property->id)
            ->where('start_date', $start_date)
            ->where('end_date', $end_date)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $advertisements);
        $advertisement = $advertisements->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $advertisement = Advertisements::factory()->create();

        $response = $this->get(route('advertisements.show', $advertisement));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AdvertisementsController::class,
            'update',
            \App\Http\Requests\AdvertisementsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $advertisement = Advertisements::factory()->create();
        $user = User::factory()->create();
        $ad_package = AdPackage::factory()->create();
        $property = Property::factory()->create();
        $start_date = Carbon::parse($this->faker->date());
        $end_date = Carbon::parse($this->faker->date());
        $status = $this->faker->word();

        $response = $this->put(route('advertisements.update', $advertisement), [
            'user_id' => $user->id,
            'ad_package_id' => $ad_package->id,
            'property_id' => $property->id,
            'start_date' => $start_date->toDateString(),
            'end_date' => $end_date->toDateString(),
            'status' => $status,
        ]);

        $advertisement->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($user->id, $advertisement->user_id);
        $this->assertEquals($ad_package->id, $advertisement->ad_package_id);
        $this->assertEquals($property->id, $advertisement->property_id);
        $this->assertEquals($start_date, $advertisement->start_date);
        $this->assertEquals($end_date, $advertisement->end_date);
        $this->assertEquals($status, $advertisement->status);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $advertisement = Advertisements::factory()->create();
        $advertisement = Advertisement::factory()->create();

        $response = $this->delete(route('advertisements.destroy', $advertisement));

        $response->assertNoContent();

        $this->assertModelMissing($advertisement);
    }
}
