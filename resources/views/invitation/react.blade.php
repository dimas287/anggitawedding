<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Undangan Pernikahan {{ $invitation->groom_name ?? '' }} & {{ $invitation->bride_name ?? '' }}</title>
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Undangan Pernikahan {{ $invitation->groom_name ?? '' }} & {{ $invitation->bride_name ?? '' }}">
    <meta property="og:description" content="{{ optional($invitation->reception_datetime)->isoFormat('D MMMM Y') ?? 'Merupakan suatu kehormatan bagi kami apabila Bapak/Ibu berkenan hadir.' }}">
    @if($invitation->photo_prewedding)
    <meta property="og:image" content="{{ route('invitation.media', ['slug' => $invitation->slug, 'type' => 'prewedding']) }}">
    <meta property="og:image:alt" content="Undangan pernikahan {{ $invitation->groom_name }} dan {{ $invitation->bride_name }}">
    @else
    <meta property="og:image" content="{{ asset('favicon.ico') }}">
    @endif

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Undangan Pernikahan {{ $invitation->groom_name ?? '' }} & {{ $invitation->bride_name ?? '' }}">
    <meta name="twitter:description" content="{{ optional($invitation->reception_datetime)->isoFormat('D MMMM Y') ?? 'Merupakan suatu kehormatan bagi kami apabila Bapak/Ibu berkenan hadir.' }}">
    @if($invitation->photo_prewedding)
    <meta name="twitter:image" content="{{ route('invitation.media', ['slug' => $invitation->slug, 'type' => 'prewedding']) }}">
    @else
    <meta name="twitter:image" content="{{ asset('favicon.ico') }}">
    @endif

    @viteReactRefresh
    @vite('resources/js/invitation-app/main.jsx')
</head>
<body style="margin:0;">
    <div id="invitation-root" data-invitation="{{ json_encode(['slug' => $invitation->slug ?? '']) }}"></div>
</body>
</html>
