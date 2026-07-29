<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response
            ->assertStatus(200)
            ->assertSee('Wedding Organizer Surabaya')
            ->assertDontSee('+628123456789')
            ->assertDontSee('images/logo.png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $contentSecurityPolicy = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("object-src 'none'", $contentSecurityPolicy);
        $this->assertStringContainsString("base-uri 'self'", $contentSecurityPolicy);
        $this->assertStringContainsString("frame-ancestors 'self'", $contentSecurityPolicy);
    }
}
