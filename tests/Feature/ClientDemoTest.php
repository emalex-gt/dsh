<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientDemoTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_open_a_full_size_preview_of_a_direct_demo_image(): void
    {
        $user = User::factory()->create([
            'direct_demo_name' => 'Demo privada',
            'direct_demo_desktop_image' => 'clients/direct-demos/desktop/example.png',
        ]);

        $this->actingAs($user)
            ->get(route('client.demos.index'))
            ->assertOk()
            ->assertSee('Ver imagen completa')
            ->assertSee('selectedImage')
            ->assertSee('/media/clients/direct-demos/desktop/example.png');
    }
}
