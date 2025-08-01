<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Sale;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;

class POSControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_sales_list()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();
        $product = Product::factory()->create();
        $sale = Sale::factory()->create(['user_id' => $user->id, 'store_id' => $store->id]);
        $sale->saleItems()->create([
            'product_id' => $product->id,
            'qty' => 2,
            'price' => 10000,
        ]);
        $this->actingAs($user, 'sanctum');
        $response = $this->getJson('/api/pos');
        $response->assertStatus(200)->assertJsonFragment(['id' => $sale->id]);
    }

    public function test_store_creates_sale()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();
        $product = Product::factory()->create();
        $this->actingAs($user, 'sanctum');
        $payload = [
            'store_id' => $store->id,
            'user_id' => $user->id,
            'total' => 20000,
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2,
                    'price' => 10000,
                ]
            ]
        ];
        $response = $this->postJson('/api/pos', $payload);
        $response->assertStatus(201)->assertJsonFragment(['total' => 20000]);
    }

    public function test_show_returns_sale_detail()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();
        $product = Product::factory()->create();
        $sale = Sale::factory()->create(['user_id' => $user->id, 'store_id' => $store->id]);
        $sale->saleItems()->create([
            'product_id' => $product->id,
            'qty' => 2,
            'price' => 10000,
        ]);
        $this->actingAs($user, 'sanctum');
        $response = $this->getJson('/api/pos/' . $sale->id);
        $response->assertStatus(200)->assertJsonFragment(['id' => $sale->id]);
    }

    public function test_update_modifies_sale()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();
        $product = Product::factory()->create();
        $sale = Sale::factory()->create(['user_id' => $user->id, 'store_id' => $store->id, 'total' => 10000]);
        $sale->saleItems()->create([
            'product_id' => $product->id,
            'qty' => 2,
            'price' => 10000,
        ]);
        $this->actingAs($user, 'sanctum');
        $payload = [
            'total' => 30000,
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 3,
                    'price' => 10000,
                ]
            ]
        ];
        $response = $this->putJson('/api/pos/' . $sale->id, $payload);
        $response->assertStatus(200)->assertJsonFragment(['total' => 30000]);
    }

    public function test_destroy_deletes_sale()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create();
        $product = Product::factory()->create();
        $sale = Sale::factory()->create(['user_id' => $user->id, 'store_id' => $store->id]);
        $sale->saleItems()->create([
            'product_id' => $product->id,
            'qty' => 2,
            'price' => 10000,
        ]);
        $this->actingAs($user, 'sanctum');
        $response = $this->deleteJson('/api/pos/' . $sale->id);
        $response->assertStatus(200)->assertJsonFragment(['message' => 'Transaksi penjualan dihapus']);
    }
}
