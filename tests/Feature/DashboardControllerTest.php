<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_dashboard_summary()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'salesToday', 'totalSales', 'productCount', 'attendanceToday', 'stockMutationsToday', 'userCount', 'storeCount'
                 ]);
    }
}
