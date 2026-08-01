<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_template_demo_slug_opens_the_react_preview(): void
    {
        InvitationTemplate::create([
            'name' => 'Tema Modern',
            'slug' => 'tema2',
            'demo_slug' => 'tema2-demo',
            'theme' => 'modern',
            'is_active' => true,
            'demo_content' => [
                'groom_name' => 'Rahman',
                'bride_name' => 'Nadia',
                'reception_datetime' => '2026-09-12T19:00:00+07:00',
            ],
        ]);

        $this->get('/undangan/tema2-demo')
            ->assertOk()
            ->assertViewIs('invitation.react')
            ->assertSee('Undangan Pernikahan Rahman &amp; Nadia', false)
            ->assertSee('&quot;slug&quot;:&quot;tema2-demo&quot;', false);

        $this->getJson('/api/invitations/tema2-demo')
            ->assertOk()
            ->assertJsonPath('status', 'demo')
            ->assertJsonPath('template.slug', 'tema2');
    }

    public function test_inactive_or_unknown_demo_slug_returns_not_found(): void
    {
        InvitationTemplate::create([
            'name' => 'Tema Nonaktif',
            'slug' => 'tema-nonaktif',
            'demo_slug' => 'tema-nonaktif-demo',
            'theme' => 'modern',
            'is_active' => false,
        ]);

        $this->get('/undangan/tema-nonaktif-demo')->assertNotFound();
        $this->getJson('/api/invitations/tema-nonaktif-demo')->assertNotFound();
        $this->get('/undangan/tidak-ada')->assertNotFound();
    }
}
