<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\InvitationView;
use App\Models\Rsvp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InvitationPublicController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with('template:id,name,slug')
            ->first();

        if (!$invitation) {
            $template = InvitationTemplate::query()
                ->where('demo_slug', $slug)
                ->first();

            if ($template) {
                return response()->json($this->buildDemoResponse($template));
            }

            abort(404);
        }

        $ip = request()->ip();
        if ($ip) {
            $since = Carbon::now()->subMinutes(10);
            $alreadyCountedRecently = InvitationView::query()
                ->where('invitation_id', $invitation->id)
                ->where('ip_address', $ip)
                ->where('created_at', '>=', $since)
                ->exists();

            if (!$alreadyCountedRecently) {
                DB::transaction(function () use ($invitation, $ip) {
                    $invitation->increment('view_count');
                    InvitationView::create([
                        'invitation_id' => $invitation->id,
                        'ip_address' => $ip,
                        'user_agent' => request()->userAgent(),
                    ]);
                });
            }
        }

        return response()->json([
            'id' => $invitation->id,
            'slug' => $invitation->slug,
            'template' => $invitation->template,
            'status' => $invitation->is_published ? 'published' : 'draft',
            'view_count' => (int) $invitation->view_count,
            'content' => [
                'groom_name' => $invitation->groom_name,
                'groom_short_name' => $invitation->groom_short_name,
                'bride_name' => $invitation->bride_name,
                'bride_short_name' => $invitation->bride_short_name,
                'groom_father' => $invitation->groom_father,
                'groom_mother' => $invitation->groom_mother,
                'bride_father' => $invitation->bride_father,
                'bride_mother' => $invitation->bride_mother,
                'akad_datetime' => optional($invitation->akad_datetime)->toIso8601String(),
                'akad_venue' => $invitation->akad_venue,
                'akad_address' => $invitation->akad_address,
                'reception_datetime' => optional($invitation->reception_datetime)->toIso8601String(),
                'reception_venue' => $invitation->reception_venue,
                'reception_address' => $invitation->reception_address,
                'maps_link' => $invitation->maps_link,
                'love_story' => $invitation->love_story,
                'opening_quote' => $invitation->opening_quote,
                'closing_message' => $invitation->closing_message,
                'hashtag' => $invitation->hashtag,
                'music_file_url' => $invitation->music_file ? route('invitation.media', ['slug' => $invitation->slug, 'type' => 'music']) : null,
                'photo_prewedding_url' => $invitation->photo_prewedding ? route('invitation.media', ['slug' => $invitation->slug, 'type' => 'prewedding']) : null,
                'gallery_photo_urls' => collect($invitation->gallery_photos ?? [])->map(fn ($p, $i) => route('invitation.media', ['slug' => $invitation->slug, 'type' => 'gallery', 'i' => $i]))->values(),
                'rsvp_enabled' => (bool) $invitation->rsvp_enabled,
                
                // Rich Content & Dynamic Media
                'media_files' => collect($invitation->media_files ?? [])->map(function($val, $key) use ($invitation) {
                    if (is_array($val)) {
                        return collect($val)->map(fn($v, $i) => route('invitation.media', ['slug' => $invitation->slug, 'type' => 'dynamic', 'slot' => $key, 'i' => $i]))->values();
                    }
                    return route('invitation.media', ['slug' => $invitation->slug, 'type' => 'dynamic', 'slot' => $key]);
                }),
                'love_story_items' => $invitation->love_story_items,
                'bank_accounts' => $invitation->bank_accounts,
                'qris_image_url' => $invitation->qris_image ? route('invitation.media', ['slug' => $invitation->slug, 'type' => 'qris']) : null,
                'groom_photo_url' => $invitation->groom_photo ? route('invitation.media', ['slug' => $invitation->slug, 'type' => 'groom']) : null,
                'bride_photo_url' => $invitation->bride_photo ? route('invitation.media', ['slug' => $invitation->slug, 'type' => 'bride']) : null,
                'groom_instagram' => $invitation->groom_instagram,
                'bride_instagram' => $invitation->bride_instagram,
            ],
        ]);
    }

    public function stats(string $slug): JsonResponse
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return response()->json([
            'view_count' => (int) $invitation->view_count,
            'views_total' => (int) $invitation->views()->count(),
            'rsvp' => $invitation->rsvp_stats,
        ]);
    }

    public function storeRsvp(Request $request, string $slug): JsonResponse
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        if (!$invitation->rsvp_enabled) {
            return response()->json([
                'message' => 'RSVP sedang dinonaktifkan.',
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'guests_count' => 'required|integer|min:1|max:10',
            'attendance' => 'required|in:hadir,tidak_hadir,mungkin',
            'message' => 'nullable|string|max:500',
        ]);

        $rsvp = Rsvp::create([
            'invitation_id' => $invitation->id,
            'name' => strip_tags($validated['name']),
            'phone' => strip_tags($validated['phone'] ?? null),
            'guests_count' => $validated['guests_count'],
            'attendance' => $validated['attendance'],
            'message' => strip_tags($validated['message'] ?? null),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'RSVP berhasil dikirim. Terima kasih!',
            'data' => [
                'id' => $rsvp->id,
                'name' => $rsvp->name,
                'attendance' => $rsvp->attendance,
            ],
        ]);
    }

    protected function buildDemoResponse(InvitationTemplate $template): array
    {
        $demo = $template->parsed_demo_content;

        return [
            'id' => null,
            'slug' => $template->demo_slug,
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'slug' => $template->slug,
            ],
            'status' => 'demo',
            'view_count' => 0,
            'content' => [
                'groom_name' => $demo['groom_name'],
                'groom_short_name' => $demo['groom_short_name'] ?? $demo['groom_name'],
                'bride_name' => $demo['bride_name'],
                'bride_short_name' => $demo['bride_short_name'] ?? $demo['bride_name'],
                'groom_father' => $demo['groom_father'],
                'groom_mother' => $demo['groom_mother'],
                'bride_father' => $demo['bride_father'],
                'bride_mother' => $demo['bride_mother'],
                'akad_datetime' => $demo['akad_datetime'],
                'akad_venue' => $demo['akad_venue'],
                'akad_address' => $demo['akad_address'],
                'reception_datetime' => $demo['reception_datetime'],
                'reception_venue' => $demo['reception_venue'],
                'reception_address' => $demo['reception_address'],
                'maps_link' => $demo['maps_link'],
                'love_story' => $demo['love_story'],
                'opening_quote' => $demo['opening_quote'],
                'closing_message' => $demo['closing_message'],
                'hashtag' => $demo['hashtag'],
                'music_file_url' => $demo['music_file_url'] ?? null,
                'photo_prewedding_url' => $demo['photo_prewedding_url'] ?? null,
                'gallery_photo_urls' => $demo['gallery_photo_urls'] ?? [],
                'rsvp_enabled' => (bool) ($demo['rsvp_enabled'] ?? false),
                
                // Rich Content & Dynamic Media
                'media_files' => $demo['media_files'] ?? [],
                'love_story_items' => $demo['love_story_items'] ?? [],
                'bank_accounts' => $demo['bank_accounts'] ?? [],
                'qris_image_url' => $demo['qris_image_url'] ?? null,
                'groom_photo_url' => $demo['groom_photo_url'] ?? null,
                'bride_photo_url' => $demo['bride_photo_url'] ?? null,
                'groom_instagram' => $demo['groom_instagram'] ?? null,
                'bride_instagram' => $demo['bride_instagram'] ?? null,
            ],
        ];
    }
}
