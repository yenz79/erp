<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Store;
use App\Models\Shift;

class AbsensiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_get_paginated_attendance()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        Attendance::factory()->count(5)->create();
        $response = $this->getJson('/api/absensi');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_can_create_attendance()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();
        $shift = Shift::factory()->create();
        $this->actingAs($user, 'sanctum');
        $payload = [
            'user_id' => $user->id,
            'store_id' => $store->id,
            'shift_id' => $shift->id,
            'type' => 'in',
            'timestamp' => now()->toDateTimeString()
        ];
        $response = $this->postJson('/api/absensi', $payload);
        $response->assertStatus(201)
                 ->assertJsonFragment(['type' => 'in']);
    }

    public function test_can_get_attendance_detail()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $attendance = Attendance::factory()->create();
        $response = $this->getJson('/api/absensi/' . $attendance->id);
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $attendance->id]);
    }

    public function test_can_update_attendance()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $attendance = Attendance::factory()->create(['type' => 'in']);
        $response = $this->putJson('/api/absensi/' . $attendance->id, ['type' => 'out']);
        $response->assertStatus(200)
                 ->assertJsonFragment(['type' => 'out']);
    }

    public function test_can_delete_attendance()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $attendance = Attendance::factory()->create();
        $response = $this->deleteJson('/api/absensi/' . $attendance->id);
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Absensi dihapus']);
    }
}
