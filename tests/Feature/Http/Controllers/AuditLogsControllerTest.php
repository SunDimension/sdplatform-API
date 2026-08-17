<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AuditLog;
use App\Models\AuditLogs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AuditLogsController
 */
final class AuditLogsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $auditLogs = AuditLogs::factory()->count(3)->create();

        $response = $this->get(route('audit-logs.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AuditLogsController::class,
            'store',
            \App\Http\Requests\AuditLogsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $action = $this->faker->word();
        $model = $this->faker->word();

        $response = $this->post(route('audit-logs.store'), [
            'action' => $action,
            'model' => $model,
        ]);

        $auditLogs = AuditLog::query()
            ->where('action', $action)
            ->where('model', $model)
            ->get();
        $this->assertCount(1, $auditLogs);
        $auditLog = $auditLogs->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $auditLog = AuditLogs::factory()->create();

        $response = $this->get(route('audit-logs.show', $auditLog));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AuditLogsController::class,
            'update',
            \App\Http\Requests\AuditLogsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $auditLog = AuditLogs::factory()->create();
        $action = $this->faker->word();
        $model = $this->faker->word();

        $response = $this->put(route('audit-logs.update', $auditLog), [
            'action' => $action,
            'model' => $model,
        ]);

        $auditLog->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($action, $auditLog->action);
        $this->assertEquals($model, $auditLog->model);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $auditLog = AuditLogs::factory()->create();
        $auditLog = AuditLog::factory()->create();

        $response = $this->delete(route('audit-logs.destroy', $auditLog));

        $response->assertNoContent();

        $this->assertModelMissing($auditLog);
    }
}
