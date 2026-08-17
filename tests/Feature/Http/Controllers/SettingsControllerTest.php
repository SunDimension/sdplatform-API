<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Setting;
use App\Models\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SettingsController
 */
final class SettingsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $settings = Settings::factory()->count(3)->create();

        $response = $this->get(route('settings.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SettingsController::class,
            'store',
            \App\Http\Requests\SettingsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $key = $this->faker->word();

        $response = $this->post(route('settings.store'), [
            'key' => $key,
        ]);

        $settings = Setting::query()
            ->where('key', $key)
            ->get();
        $this->assertCount(1, $settings);
        $setting = $settings->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $setting = Settings::factory()->create();

        $response = $this->get(route('settings.show', $setting));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SettingsController::class,
            'update',
            \App\Http\Requests\SettingsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $setting = Settings::factory()->create();
        $key = $this->faker->word();

        $response = $this->put(route('settings.update', $setting), [
            'key' => $key,
        ]);

        $setting->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($key, $setting->key);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $setting = Settings::factory()->create();
        $setting = Setting::factory()->create();

        $response = $this->delete(route('settings.destroy', $setting));

        $response->assertNoContent();

        $this->assertModelMissing($setting);
    }
}
