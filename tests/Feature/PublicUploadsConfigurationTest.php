<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicUploadsConfigurationTest extends TestCase
{
    public function test_public_uploads_are_served_without_a_symbolic_link(): void
    {
        $disk = config('filesystems.disks.public');

        $this->assertSame(storage_path('app/public'), $disk['root']);
        $this->assertSame(rtrim(config('app.url'), '/').'/media', $disk['url']);

        Storage::fake('public');
        Storage::disk('public')->put('demos/previews/example.png', 'image-content');

        $this->get('/media/demos/previews/example.png')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->get('/media/%2E%2E/.env')->assertNotFound();
    }
}
