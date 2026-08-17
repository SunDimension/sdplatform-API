<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Notification;
use App\Models\Notifications;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\NotificationsController
 */
final class NotificationsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $notifications = Notifications::factory()->count(3)->create();

        $response = $this->get(route('notifications.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NotificationsController::class,
            'store',
            \App\Http\Requests\NotificationsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $user = User::factory()->create();
        $title = $this->faker->sentence(4);
        $message = $this->faker->text();
        $notification_type = $this->faker->word();

        $response = $this->post(route('notifications.store'), [
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'notification_type' => $notification_type,
        ]);

        $notifications = Notification::query()
            ->where('user_id', $user->id)
            ->where('title', $title)
            ->where('message', $message)
            ->where('notification_type', $notification_type)
            ->get();
        $this->assertCount(1, $notifications);
        $notification = $notifications->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $notification = Notifications::factory()->create();

        $response = $this->get(route('notifications.show', $notification));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\NotificationsController::class,
            'update',
            \App\Http\Requests\NotificationsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $notification = Notifications::factory()->create();
        $user = User::factory()->create();
        $title = $this->faker->sentence(4);
        $message = $this->faker->text();
        $notification_type = $this->faker->word();

        $response = $this->put(route('notifications.update', $notification), [
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'notification_type' => $notification_type,
        ]);

        $notification->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($user->id, $notification->user_id);
        $this->assertEquals($title, $notification->title);
        $this->assertEquals($message, $notification->message);
        $this->assertEquals($notification_type, $notification->notification_type);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $notification = Notifications::factory()->create();
        $notification = Notification::factory()->create();

        $response = $this->delete(route('notifications.destroy', $notification));

        $response->assertNoContent();

        $this->assertModelMissing($notification);
    }
}
