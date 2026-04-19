<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Consultation;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IdempotencyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_booking_submission_is_deduplicated(): void
    {
        if (!Schema::hasColumn('bookings', 'groom_short_name')) {
            $this->markTestSkipped('Kolom groom_short_name belum ada di schema lokal. Jalankan migrasi terbaru.');
        }

        $user = User::factory()->create([
            'phone' => '08123456789',
        ]);

        $package = Package::create([
            'name' => 'Paket Silver',
            'slug' => 'paket-silver',
            'tier' => 'silver',
            'price' => 15000000,
            'description' => 'Test package',
            'features' => [],
            'is_active' => true,
        ]);

        $payload = [
            'package_id' => $package->id,
            'event_date' => now()->addDays(10)->toDateString(),
            'venue' => 'Gedung Test',
            'venue_address' => 'Jl. Venue',
            'phone' => '08123456789',
            'estimated_guests' => 100,
            'groom_name' => 'Groom',
            'groom_short_name' => 'Groom',
            'bride_name' => 'Bride',
            'bride_short_name' => 'Bride',
        ];

        $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\EnsureProfileComplete::class)
            ->post(route('booking.store'), $payload)
            ->assertRedirect();

        $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\EnsureProfileComplete::class)
            ->post(route('booking.store'), $payload)
            ->assertRedirect();

        $this->assertSame(1, Booking::count());
    }

    public function test_invitation_order_is_deduplicated(): void
    {
        if (!Schema::hasColumn('bookings', 'groom_short_name')) {
            $this->markTestSkipped('Kolom groom_short_name belum ada di schema lokal. Jalankan migrasi terbaru.');
        }

        $user = User::factory()->create();

        $package = Package::create([
            'name' => 'Undangan Digital',
            'slug' => 'undangan-digital',
            'tier' => 'silver',
            'price' => 0,
            'description' => 'Digital invitation package',
            'features' => [],
            'is_active' => true,
        ]);

        $template = InvitationTemplate::create([
            'name' => 'Template A',
            'slug' => 'template-a',
            'theme' => 'classic',
            'is_active' => true,
        ]);

        $payload = [
            'template_id' => $template->id,
            'groom_name' => 'Groom',
            'groom_short_name' => 'Groom',
            'bride_name' => 'Bride',
            'bride_short_name' => 'Bride',
        ];

        $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\EnsureProfileComplete::class)
            ->post(route('invitation-order.checkout'), $payload)
            ->assertRedirect();

        $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\EnsureProfileComplete::class)
            ->post(route('invitation-order.checkout'), $payload)
            ->assertRedirect();

        $this->assertSame(1, Booking::count());
        $this->assertSame(1, Invitation::count());
    }

    public function test_consultation_submission_is_deduplicated(): void
    {
        $user = User::factory()->create([
            'phone' => '08123456789',
        ]);

        $payload = [
            'name' => 'User Test',
            'email' => $user->email,
            'phone' => '08123456789',
            'preferred_date' => now()->addDays(3)->toDateString(),
            'preferred_time' => '10:00',
            'event_date' => now()->addMonths(2)->toDateString(),
            'consultation_type' => 'online',
            'message' => 'Test',
        ];

        $this->actingAs($user)->post(route('consultation.store'), $payload)
            ->assertRedirect();

        $this->actingAs($user)->post(route('consultation.store'), $payload)
            ->assertRedirect();

        $this->assertSame(1, Consultation::count());
    }
}
