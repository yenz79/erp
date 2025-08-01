<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\StockMutation;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;

class GudangControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_get_paginated_stock_mutations()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        StockMutation::factory()->count(5)->create();
        $response = $this->getJson('/api/gudang');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data', 'links', 'meta'
                 ]);
    }

    public function test_can_create_stock_mutation()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $store = Store::factory()->create();
        $this->actingAs($user, 'sanctum');
        $payload = [
            'product_id' => $product->id,
            'store_id' => $store->id,
            'user_id' => $user->id,
            'qty' => 10,
            'type' => 'in',
            'description' => 'Stok masuk awal'
        ];
        $response = $this->postJson('/api/gudang', $payload);
        $response->assertStatus(201)
                 ->assertJsonFragment(['qty' => 10, 'type' => 'in']);
    }

    public function test_can_get_stock_mutation_detail()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $mutation = StockMutation::factory()->create();
        $response = $this->getJson('/api/gudang/' . $mutation->id);
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $mutation->id]);
    }

    public function test_can_update_stock_mutation()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $mutation = StockMutation::factory()->create(['qty' => 5]);
        $response = $this->putJson('/api/gudang/' . $mutation->id, ['qty' => 15]);
        $response->assertStatus(200)
                 ->assertJsonFragment(['qty' => 15]);
    }

    public function test_can_delete_stock_mutation()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $mutation = StockMutation::factory()->create();
        $response = $this->deleteJson('/api/gudang/' . $mutation->id);
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Mutasi stok dihapus']);
    }
}
