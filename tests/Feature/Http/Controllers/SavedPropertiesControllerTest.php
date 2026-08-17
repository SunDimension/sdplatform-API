<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Property;
use App\Models\SavedProperties;
use App\Models\SavedProperty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SavedPropertiesController
 */
final class SavedPropertiesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $savedProperties = SavedProperties::factory()->count(3)->create();

        $response = $this->get(route('saved-properties.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SavedPropertiesController::class,
            'store',
            \App\Http\Requests\SavedPropertiesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('saved-properties.store'), [
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);

        $savedProperties = SavedProperty::query()
            ->where('property_id', $property->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $savedProperties);
        $savedProperty = $savedProperties->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $savedProperty = SavedProperties::factory()->create();

        $response = $this->get(route('saved-properties.show', $savedProperty));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SavedPropertiesController::class,
            'update',
            \App\Http\Requests\SavedPropertiesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $savedProperty = SavedProperties::factory()->create();
        $property = Property::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('saved-properties.update', $savedProperty), [
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);

        $savedProperty->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $savedProperty->property_id);
        $this->assertEquals($user->id, $savedProperty->user_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $savedProperty = SavedProperties::factory()->create();
        $savedProperty = SavedProperty::factory()->create();

        $response = $this->delete(route('saved-properties.destroy', $savedProperty));

        $response->assertNoContent();

        $this->assertModelMissing($savedProperty);
    }
}
