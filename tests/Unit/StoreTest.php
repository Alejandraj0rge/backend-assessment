<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        // Create a user with a known password
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // Ensure the password is hashed
        ]);

        // Attempt to login with the correct credentials
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);


        $response->assertStatus(200);
        return $response['token'];
    }

    public function test_can_create_store()
    {
        $token = $this->authenticate();

        $response = $this->postJson('/api/stores', [
            'name' => 'Sample Store',
            'address' => '123 Main St',
            'active' => true
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id', 'name', 'address', 'active', 'created_at', 'updated_at'
                 ]);
    }

    public function test_can_get_stores()
    {
        $token = $this->authenticate();

        Store::factory()->count(3)->create();

        $response = $this->getJson('/api/stores', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_update_store()
    {
        $token = $this->authenticate();

        $store = Store::factory()->create();

        $response = $this->putJson("/api/stores/{$store->id}", [
            'name' => 'Updated Store Name',
            'address' => '456 Main St',
            'active' => false
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated Store Name',
                     'address' => '456 Main St',
                     'active' => false
                 ]);
    }

    public function test_can_delete_store()
    {
        $token = $this->authenticate();
        $store = Store::factory()->create();

        $response = $this->deleteJson("/api/stores/{$store->id}", [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(204);
    }
}