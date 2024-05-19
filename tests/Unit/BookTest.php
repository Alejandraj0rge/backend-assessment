<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class BookTest extends TestCase
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

    public function test_can_create_book()
    {
        $token = $this->authenticate();

        $response = $this->postJson('/api/books', [
            'name' => 'Sample Book',
            'isbn' => '1234567890',
            'value' => 19.99,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id', 'name', 'isbn', 'value', 'created_at', 'updated_at'
                ]);
    }

    public function test_can_get_books()
    {
        $token = $this->authenticate();

        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/books', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_update_book()
    {
        $token = $this->authenticate();

        $book = Book::factory()->create();

        $response = $this->putJson("/api/books/{$book->id}", [
            'name' => 'Updated Book Name',
            'value' => 25.00
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);
        

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated Book Name',
                     'value' => 25.00
                 ]);
    }

    public function test_can_delete_book()
    {
        $token = $this->authenticate();
        
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$book->id}", [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(204);
    }
}
