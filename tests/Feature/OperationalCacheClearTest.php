<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OperationalCacheClearTest extends TestCase
{
    use RefreshDatabase;

    private const CACHE_COMMANDS = [
        'optimize:clear',
        'config:clear',
        'cache:clear',
        'route:clear',
        'view:clear',
    ];

    public function test_guest_cannot_clear_operational_caches(): void
    {
        $this->expectNoCacheCommands();

        $this->post('/_ops/clear')
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_client_cannot_clear_operational_caches(): void
    {
        $this->expectNoCacheCommands();

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post('/_ops/clear')
            ->assertForbidden();
    }

    public function test_admin_can_clear_operational_caches(): void
    {
        $this->expectCacheCommands();

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->post('/_ops/clear')
            ->assertOk()
            ->assertSee('Caches limpiadas correctamente.');
    }

    public function test_get_cannot_clear_operational_caches(): void
    {
        $this->expectNoCacheCommands();

        $this->get('/_ops/clear')
            ->assertMethodNotAllowed();
    }

    private function expectNoCacheCommands(): void
    {
        foreach (self::CACHE_COMMANDS as $command) {
            Artisan::shouldReceive('call')
                ->with($command)
                ->never();
        }
    }

    private function expectCacheCommands(): void
    {
        foreach (self::CACHE_COMMANDS as $command) {
            Artisan::shouldReceive('call')
                ->with($command)
                ->once()
                ->andReturn(0);
        }
    }
}
