<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Notification;
use App\Models\User;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_notifications()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        Notification::factory()->count(3)->create();
        $response = $this->getJson('/api/notification');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_can_create_notification()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $payload = [
            'user_id' => $user->id,
            'title' => 'Test Notif',
            'body' => 'Isi notifikasi'
        ];
        $response = $this->postJson('/api/notification', $payload);
        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Test Notif']);
    }

    public function test_can_get_notification_detail()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $notif = Notification::factory()->create();
        $response = $this->getJson('/api/notification/' . $notif->id);
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $notif->id]);
    }

    public function test_can_update_notification()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $notif = Notification::factory()->create(['title' => 'Old']);
        $response = $this->putJson('/api/notification/' . $notif->id, ['title' => 'New']);
        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'New']);
    }

    public function test_can_delete_notification()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $notif = Notification::factory()->create();
        $response = $this->deleteJson('/api/notification/' . $notif->id);
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Notifikasi dihapus']);
    }
}
