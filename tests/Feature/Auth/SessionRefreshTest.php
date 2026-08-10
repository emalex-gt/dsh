<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionRefreshTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_refresh_endpoint_returns_a_valid_csrf_token(): void
    {
        $response = $this->getJson(route('session.refresh'));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'expires_in_minutes',
            ]);

        $this->assertSame(csrf_token(), $response->json('token'));
    }
}
