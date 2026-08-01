<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\InvitationTemplate;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Throwable;

class InvitationPageController extends Controller
{
    public function show(string $slug): View
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if ($invitation) {
            return view('invitation.react', $this->pageData(
                slug: $invitation->slug,
                groomName: $invitation->groom_name,
                brideName: $invitation->bride_name,
                receptionDatetime: $invitation->reception_datetime,
                imageUrl: $invitation->photo_prewedding
                    ? route('invitation.media', ['slug' => $invitation->slug, 'type' => 'prewedding'])
                    : null,
            ));
        }

        $template = InvitationTemplate::query()
            ->where('demo_slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $demo = $template->parsed_demo_content;

        return view('invitation.react', $this->pageData(
            slug: $template->demo_slug,
            groomName: $demo['groom_name'] ?? 'Mempelai Pria',
            brideName: $demo['bride_name'] ?? 'Mempelai Wanita',
            receptionDatetime: $demo['reception_datetime'] ?? null,
            imageUrl: $demo['photo_prewedding_url'] ?? null,
        ));
    }

    private function pageData(
        string $slug,
        string $groomName,
        string $brideName,
        mixed $receptionDatetime,
        ?string $imageUrl,
    ): array {
        $title = "Undangan Pernikahan {$groomName} & {$brideName}";
        $description = $this->formatReceptionDate($receptionDatetime)
            ?? 'Merupakan suatu kehormatan bagi kami apabila Bapak/Ibu berkenan hadir.';

        return [
            'pageSlug' => $slug,
            'pageTitle' => $title,
            'pageDescription' => $description,
            'pageImageUrl' => $imageUrl ?: asset('favicon.ico'),
            'pageImageAlt' => "Undangan pernikahan {$groomName} dan {$brideName}",
        ];
    }

    private function formatReceptionDate(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value)->locale('id')->translatedFormat('j F Y');
        } catch (Throwable) {
            return null;
        }
    }
}
