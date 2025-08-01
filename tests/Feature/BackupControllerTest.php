<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class BackupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_backups()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->getJson('/api/backup');
        $response->assertStatus(200)
                 ->assertJsonStructure([]); // Struktur file backup
    }

    public function test_can_create_backup()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->postJson('/api/backup');
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Backup berhasil']);
    }

    public function test_can_delete_backup()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $filename = 'backup_test.sql';
        // Simulasi file backup
        \Storage::disk('backups')->put($filename, 'dummy content');
        $response = $this->deleteJson('/api/backup/' . $filename);
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Backup dihapus']);
    }
}
