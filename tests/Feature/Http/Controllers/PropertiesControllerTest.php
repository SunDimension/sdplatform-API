<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Owner;
use App\Models\Properties;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertiesController
 */
final class PropertiesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $properties = Properties::factory()->count(3)->create();

        $response = $this->get(route('properties.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertiesController::class,
            'store',
            \App\Http\Requests\PropertiesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $owner = Owner::factory()->create();
        $property_type = PropertyType::factory()->create();
        $category = Category::factory()->create();
        $status = Status::factory()->create();
        $purpose = $this->faker->word();
        $description = $this->faker->text();
        $price = $this->faker->randomFloat(/** float_attributes **/);
        $title = $this->faker->sentence(4);
        $currency = $this->faker->word();
        $negotiable = $this->faker->boolean();
        $furnished = $this->faker->boolean();
        $serviced = $this->faker->boolean();
        $pet_friendly = $this->faker->boolean();
        $country = Country::factory()->create();
        $state = State::factory()->create();
        $city = City::factory()->create();
        $featured = $this->faker->boolean();
        $premium = $this->faker->boolean();
        $verified = $this->faker->boolean();
        $views = $this->faker->numberBetween(-10000, 10000);
        $likes = $this->faker->numberBetween(-10000, 10000);

        $response = $this->post(route('properties.store'), [
            'owner_id' => $owner->id,
            'property_type_id' => $property_type->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
            'purpose' => $purpose,
            'description' => $description,
            'price' => $price,
            'title' => $title,
            'currency' => $currency,
            'negotiable' => $negotiable,
            'furnished' => $furnished,
            'serviced' => $serviced,
            'pet_friendly' => $pet_friendly,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'featured' => $featured,
            'premium' => $premium,
            'verified' => $verified,
            'views' => $views,
            'likes' => $likes,
        ]);

        $properties = Property::query()
            ->where('owner_id', $owner->id)
            ->where('property_type_id', $property_type->id)
            ->where('category_id', $category->id)
            ->where('status_id', $status->id)
            ->where('purpose', $purpose)
            ->where('description', $description)
            ->where('price', $price)
            ->where('title', $title)
            ->where('currency', $currency)
            ->where('negotiable', $negotiable)
            ->where('furnished', $furnished)
            ->where('serviced', $serviced)
            ->where('pet_friendly', $pet_friendly)
            ->where('country_id', $country->id)
            ->where('state_id', $state->id)
            ->where('city_id', $city->id)
            ->where('featured', $featured)
            ->where('premium', $premium)
            ->where('verified', $verified)
            ->where('views', $views)
            ->where('likes', $likes)
            ->get();
        $this->assertCount(1, $properties);
        $property = $properties->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $property = Properties::factory()->create();

        $response = $this->get(route('properties.show', $property));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertiesController::class,
            'update',
            \App\Http\Requests\PropertiesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $property = Properties::factory()->create();
        $owner = Owner::factory()->create();
        $property_type = PropertyType::factory()->create();
        $category = Category::factory()->create();
        $status = Status::factory()->create();
        $purpose = $this->faker->word();
        $description = $this->faker->text();
        $price = $this->faker->randomFloat(/** float_attributes **/);
        $title = $this->faker->sentence(4);
        $currency = $this->faker->word();
        $negotiable = $this->faker->boolean();
        $furnished = $this->faker->boolean();
        $serviced = $this->faker->boolean();
        $pet_friendly = $this->faker->boolean();
        $country = Country::factory()->create();
        $state = State::factory()->create();
        $city = City::factory()->create();
        $featured = $this->faker->boolean();
        $premium = $this->faker->boolean();
        $verified = $this->faker->boolean();
        $views = $this->faker->numberBetween(-10000, 10000);
        $likes = $this->faker->numberBetween(-10000, 10000);

        $response = $this->put(route('properties.update', $property), [
            'owner_id' => $owner->id,
            'property_type_id' => $property_type->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
            'purpose' => $purpose,
            'description' => $description,
            'price' => $price,
            'title' => $title,
            'currency' => $currency,
            'negotiable' => $negotiable,
            'furnished' => $furnished,
            'serviced' => $serviced,
            'pet_friendly' => $pet_friendly,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'featured' => $featured,
            'premium' => $premium,
            'verified' => $verified,
            'views' => $views,
            'likes' => $likes,
        ]);

        $property->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($owner->id, $property->owner_id);
        $this->assertEquals($property_type->id, $property->property_type_id);
        $this->assertEquals($category->id, $property->category_id);
        $this->assertEquals($status->id, $property->status_id);
        $this->assertEquals($purpose, $property->purpose);
        $this->assertEquals($description, $property->description);
        $this->assertEquals($price, $property->price);
        $this->assertEquals($title, $property->title);
        $this->assertEquals($currency, $property->currency);
        $this->assertEquals($negotiable, $property->negotiable);
        $this->assertEquals($furnished, $property->furnished);
        $this->assertEquals($serviced, $property->serviced);
        $this->assertEquals($pet_friendly, $property->pet_friendly);
        $this->assertEquals($country->id, $property->country_id);
        $this->assertEquals($state->id, $property->state_id);
        $this->assertEquals($city->id, $property->city_id);
        $this->assertEquals($featured, $property->featured);
        $this->assertEquals($premium, $property->premium);
        $this->assertEquals($verified, $property->verified);
        $this->assertEquals($views, $property->views);
        $this->assertEquals($likes, $property->likes);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $property = Properties::factory()->create();
        $property = Property::factory()->create();

        $response = $this->delete(route('properties.destroy', $property));

        $response->assertNoContent();

        $this->assertModelMissing($property);
    }
}
