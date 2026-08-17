<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use App\Models\Reviewer;
use App\Models\Reviews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ReviewsController
 */
final class ReviewsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $reviews = Reviews::factory()->count(3)->create();

        $response = $this->get(route('reviews.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReviewsController::class,
            'store',
            \App\Http\Requests\ReviewsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $reviewer = Reviewer::factory()->create();
        $rating = $this->faker->numberBetween(-10000, 10000);

        $response = $this->post(route('reviews.store'), [
            'property_id' => $property->id,
            'reviewer_id' => $reviewer->id,
            'rating' => $rating,
        ]);

        $reviews = Review::query()
            ->where('property_id', $property->id)
            ->where('reviewer_id', $reviewer->id)
            ->where('rating', $rating)
            ->get();
        $this->assertCount(1, $reviews);
        $review = $reviews->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $review = Reviews::factory()->create();

        $response = $this->get(route('reviews.show', $review));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReviewsController::class,
            'update',
            \App\Http\Requests\ReviewsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $review = Reviews::factory()->create();
        $property = Property::factory()->create();
        $reviewer = Reviewer::factory()->create();
        $rating = $this->faker->numberBetween(-10000, 10000);

        $response = $this->put(route('reviews.update', $review), [
            'property_id' => $property->id,
            'reviewer_id' => $reviewer->id,
            'rating' => $rating,
        ]);

        $review->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $review->property_id);
        $this->assertEquals($reviewer->id, $review->reviewer_id);
        $this->assertEquals($rating, $review->rating);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $review = Reviews::factory()->create();
        $review = Review::factory()->create();

        $response = $this->delete(route('reviews.destroy', $review));

        $response->assertNoContent();

        $this->assertModelMissing($review);
    }
}
