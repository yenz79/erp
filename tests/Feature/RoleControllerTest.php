<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_get_roles()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        Role::factory()->count(3)->create();
        $response = $this->getJson('/api/role');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    public function test_can_create_role()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $payload = [
            'name' => 'Kasir',
            'description' => 'Role kasir'
        ];
        $response = $this->postJson('/api/role', $payload);
        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Kasir']);
    }

    public function test_can_get_role_detail()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $role = Role::factory()->create();
        $response = $this->getJson('/api/role/' . $role->id);
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $role->id]);
    }

    public function test_can_update_role()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $role = Role::factory()->create(['name' => 'Kasir']);
        $response = $this->putJson('/api/role/' . $role->id, ['name' => 'Admin']);
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Admin']);
    }

    public function test_can_delete_role()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $role = Role::factory()->create();
        $response = $this->deleteJson('/api/role/' . $role->id);
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Role dihapus']);
    }
}
