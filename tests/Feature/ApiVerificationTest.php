<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\ServiceType;
use App\Models\Service;
use App\Models\Perahera;
use App\Models\BlogPost;
use Laravel\Sanctum\Sanctum;

class ApiVerificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        // Seed necessary data
        ServiceType::create(['name' => 'Photographer']);
        ServiceType::create(['name' => 'Dancers']);
    }

    /**
     * Test Authentication Flow
     */
    public function test_authentication_flow()
    {
        // 1. Register
        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'user'
        ]);
        $response->assertStatus(201);

        // 2. Login
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        $response->assertStatus(200)->assertJsonStructure(['token']);
        $token = $response->json('token');

        // 3. Me
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/me');
        $response->assertStatus(200)
                 ->assertJson(['email' => 'test@example.com']);

        // 4. Logout
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');
        $response->assertStatus(200);
    }

    /**
     * Test Service Provider Flow
     */
    public function test_service_provider_flow()
    {
        $type = ServiceType::first();
        
        // Register Provider
        $this->postJson('/api/register', [
            'username' => 'provider',
            'email' => 'provider@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'service_provider',
            'service_type_id' => $type->id
        ])->assertStatus(201);

        $response = $this->postJson('/api/login', [
            'email' => 'provider@example.com',
            'password' => 'password123'
        ]);
        $token = $response->json('token');
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Create Service
        $serviceData = [
            'name' => 'My Photography',
            'short_description' => 'Best photos',
            'description' => 'Detailed description here',
            'location' => 'Kandy',
            'phone' => '0771234567',
            'price' => 5000,
            'service_type_id' => $type->id,
            'status' => 'active'
        ];

        $response = $this->withHeaders($headers)->postJson('/api/services', $serviceData);
        $response->assertStatus(201);
        $serviceId = $response->json('service.id');

        // Update Service
        $response = $this->withHeaders($headers)->putJson("/api/services/{$serviceId}", [
            'name' => 'Updated Photography'
        ]);
        $response->assertStatus(200);

        // Public View
        $this->getJson('/api/services')->assertStatus(200);
        $this->getJson("/api/services/{$serviceId}")
             ->assertStatus(200)
             ->assertJson(['data' => ['name' => 'Updated Photography']]); // Check Resource response structure if needed

        // Delete Service
        $this->withHeaders($headers)->deleteJson("/api/services/{$serviceId}")
             ->assertStatus(200);
    }

    /**
     * Test Organizer Perahera Flow
     */
    public function test_organizer_perahera_flow()
    {
        // Register Organizer
        $this->postJson('/api/register', [
            'username' => 'organizer',
            'email' => 'organizer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'organizer'
        ])->assertStatus(201);

        $token = $this->postJson('/api/login', [
            'email' => 'organizer@example.com',
            'password' => 'password123'
        ])->json('token');
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Create Perahera
        $peraheraData = [
            'name' => 'Kandy Perahera',
            'location' => 'Kandy',
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'event_time' => '18:00',
            'short_description' => 'A grand event',
            'description' => 'Full description of the Kandy Perahera',
            'status' => 'active'
        ];

        $response = $this->withHeaders($headers)->postJson('/api/peraheras', $peraheraData);
        $response->assertStatus(201);
        $peraheraId = $response->json('id');

        // Update Perahera
        $this->withHeaders($headers)->putJson("/api/peraheras/{$peraheraId}", [
            'name' => 'Grand Kandy Perahera'
        ])->assertStatus(200);

        // Public View
        $this->getJson('/api/peraheras')->assertStatus(200);
        
        // Delete
        $this->withHeaders($headers)->deleteJson("/api/peraheras/{$peraheraId}")
             ->assertStatus(200);
    }

    /**
     * Test Blog Post Flow
     */
    public function test_blog_post_flow()
    {
        // User Login
        $user = User::factory()->create(['user_type' => 'user']);
        $token = $user->createToken('test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Create Post
        $postData = [
            'title' => 'My Experience',
            'short_description' => 'It was great',
            'content' => 'Full story here...',
            'status' => 'published'
        ];

        $response = $this->withHeaders($headers)->postJson('/api/blog-posts', $postData);
        $response->assertStatus(201);
        $postId = $response->json('id');

        // Public View
        $this->getJson('/api/blog-posts')->assertStatus(200);
        $this->getJson("/api/blog-posts/{$postId}")->assertStatus(200);

        // Delete
        $this->withHeaders($headers)->deleteJson("/api/blog-posts/{$postId}")
             ->assertStatus(200);
    }
}
