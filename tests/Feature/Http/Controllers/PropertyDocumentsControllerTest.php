<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\PropertyDocuments;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PropertyDocumentsController
 */
final class PropertyDocumentsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $propertyDocuments = PropertyDocuments::factory()->count(3)->create();

        $response = $this->get(route('property-documents.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyDocumentsController::class,
            'store',
            \App\Http\Requests\PropertyDocumentsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $property = Property::factory()->create();
        $title = $this->faker->sentence(4);
        $document_type = $this->faker->word();
        $verified = $this->faker->boolean();
        $document_url = $this->faker->word();

        $response = $this->post(route('property-documents.store'), [
            'property_id' => $property->id,
            'title' => $title,
            'document_type' => $document_type,
            'verified' => $verified,
            'document_url' => $document_url,
        ]);

        $propertyDocuments = PropertyDocument::query()
            ->where('property_id', $property->id)
            ->where('title', $title)
            ->where('document_type', $document_type)
            ->where('verified', $verified)
            ->where('document_url', $document_url)
            ->get();
        $this->assertCount(1, $propertyDocuments);
        $propertyDocument = $propertyDocuments->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $propertyDocument = PropertyDocuments::factory()->create();

        $response = $this->get(route('property-documents.show', $propertyDocument));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PropertyDocumentsController::class,
            'update',
            \App\Http\Requests\PropertyDocumentsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $propertyDocument = PropertyDocuments::factory()->create();
        $property = Property::factory()->create();
        $title = $this->faker->sentence(4);
        $document_type = $this->faker->word();
        $verified = $this->faker->boolean();
        $document_url = $this->faker->word();

        $response = $this->put(route('property-documents.update', $propertyDocument), [
            'property_id' => $property->id,
            'title' => $title,
            'document_type' => $document_type,
            'verified' => $verified,
            'document_url' => $document_url,
        ]);

        $propertyDocument->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($property->id, $propertyDocument->property_id);
        $this->assertEquals($title, $propertyDocument->title);
        $this->assertEquals($document_type, $propertyDocument->document_type);
        $this->assertEquals($verified, $propertyDocument->verified);
        $this->assertEquals($document_url, $propertyDocument->document_url);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $propertyDocument = PropertyDocuments::factory()->create();
        $propertyDocument = PropertyDocument::factory()->create();

        $response = $this->delete(route('property-documents.destroy', $propertyDocument));

        $response->assertNoContent();

        $this->assertModelMissing($propertyDocument);
    }
}
