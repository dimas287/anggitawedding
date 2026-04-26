<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
    @php
        /* ══════ ADAPTIVE ENGINE ══════ */
        $sections     = $package->feature_sections;
        $totalItems   = collect($sections)->sum(fn($s) => count($s['items'] ?? []));
        $numSections  = count($sections);

        /* Content volume weighting: Items + (Sections * 4) to account for headers */
        $weight = $totalItems + ($numSections * 4);

        if      ($weight <= 12) { $tier='xl'; $iF=13;  $iSp=8; $nF=40; $pF=46; $cPad='14px 16px'; $hPad=28; }
        elseif  ($weight <= 24) { $tier='lg'; $iF=11;  $iSp=6; $nF=36; $pF=40; $cPad='11px 14px'; $hPad=24; }
        elseif  ($weight <= 38) { $tier='md'; $iF=10;  $iSp=5; $nF=30; $pF=34; $cPad='9px 12px';  $hPad=20; }
        elseif  ($weight <= 52) { $tier='sm'; $iF=8.5; $iSp=4; $nF=26; $pF=30; $cPad='7px 10px';  $hPad=16; }
        elseif  ($weight <= 70) { $tier='xs'; $iF=7.5; $iSp=3; $nF=22; $pF=26; $cPad='5px 8px';   $hPad=12; }
        else                    { $tier='xxs';$iF=6.5; $iSp=2; $nF=18; $pF=22; $cPad='4px 6px';   $hPad=10; }

        /* Always 2 columns for sections */
        $rowPairs = array_chunk($sections, 2);
    @endphp

<style>
@page { margin:0; size:A4 portrait; }
* { box-sizing:border-box; margin:0; padding:0; }

body {
    font-family:'DejaVu Sans', Arial, sans-serif;
    background:#FAFAF7;
    color:#1C0900;
    width:210mm;
    height:297mm;
    overflow:hidden;
}

/* ── GOLD STRIPE ── */
.stripe-top {
    height:8px;
    background:#D4AF37;
    border-bottom:2px solid #8B6914;
}

/* ── HEADER ── */
.header {
    background:#1C0900;
    padding:11px 28px;
}
.hd-t { width:100%; border-collapse:collapse; }
.hd-t td { vertical-align:middle; }
.hd-brand { font-size:13px; font-weight:bold; color:#D4AF37; letter-spacing:2.5px; text-transform:uppercase; }
.hd-sub   { font-size:7px; color:rgba(212,175,55,.5); letter-spacing:2px; text-transform:uppercase; margin-top:2px; }
.hd-ct    { font-size:7px; color:rgba(255,255,255,.4); text-align:right; line-height:1.7; }

/* ── HERO ── */
.hero {
    background:#1C0900;
    text-align:center;
    padding:{{ $hPad }}px 28px {{ (int)($hPad*.75) }}px;
    border-bottom:3px solid #D4AF37;
    position:relative;
}
.hero::before {
    content:'';
    position:absolute; top:0; left:0; right:0; bottom:0;
    background:radial-gradient(ellipse at 50% 0%, rgba(212,175,55,.12) 0%, transparent 70%);
}
.cat-pill {
    display:inline-block;
    border:1px solid rgba(212,175,55,.35);
    border-radius:30px;
    padding:2px 14px;
    font-size:7px;
    color:rgba(212,175,55,.75);
    letter-spacing:3px;
    text-transform:uppercase;
    margin-bottom:8px;
}
.pkg-name {
    font-size:{{ $nF }}px;
    font-weight:bold;
    color:#FFFFFF;
    line-height:1.1;
    margin-bottom:8px;
    letter-spacing:.5px;
}
.tier-badge {
    display:inline-block;
    padding:3px 16px;
    border-radius:20px;
    font-size:7.5px;
    font-weight:bold;
    letter-spacing:2.5px;
    text-transform:uppercase;
    margin-bottom:14px;
}
.t-silver { background:rgba(180,190,200,.15); color:#C0CDD8; border:1px solid rgba(200,210,220,.3); }
.t-gold   { background:rgba(212,175,55,.15);  color:#E8C84A; border:1px solid rgba(212,175,55,.45); }
.t-prem   { background:rgba(167,139,250,.15); color:#C4AFFF; border:1px solid rgba(167,139,250,.35); }

/* PRICE BOX */
.price-box {
    display:inline-block;
    border:1px solid rgba(212,175,55,.5);
    border-radius:12px;
    padding:10px 32px;
    background:rgba(212,175,55,.07);
}
.p-label { font-size:7.5px; color:rgba(212,175,55,.65); letter-spacing:3px; text-transform:uppercase; margin-bottom:2px; }
.p-strike{ font-size:11px; color:rgba(255,255,255,.3); text-decoration:line-through; }
.p-main  { font-size:{{ $pF }}px; font-weight:bold; color:#E8C84A; line-height:1; margin-bottom:4px; }
.p-dp    { font-size:8px; color:rgba(255,255,255,.45); }
.p-dp strong{ color:rgba(212,175,55,.8); }
.promo-tag {
    display:inline-block;
    background:rgba(236,72,153,.2);
    border:1px solid rgba(236,72,153,.4);
    color:#FBAFCF;
    font-size:7.5px; font-weight:bold;
    letter-spacing:1.5px; text-transform:uppercase;
    padding:2px 10px; border-radius:20px;
    margin-bottom:6px;
}

/* ── META (desc + badge) ── */
.meta {
    background:#FAFAF7;
    padding:{{ (int)($hPad * .55) }}px 28px {{ (int)($hPad * .4) }}px;
    text-align:center;
    border-bottom:1px solid rgba(201,168,76,.2);
}
.desc-txt { font-size:{{ max(8, $iF - 1) }}px; color:#5C3A10; line-height:1.6; margin-bottom:6px; }
.inv-badge {
    display:inline-block;
    background:#FFF8E7;
    border:1px solid #D4AF37;
    border-radius:6px;
    padding:4px 12px;
    font-size:8px; font-weight:bold;
    color:#8B6914; letter-spacing:.8px; text-transform:uppercase;
}

/* ── FEATURES AREA ── */
.feat-area { padding:{{ (int)($hPad * .5) }}px 24px {{ (int)($hPad * .5) }}px; }
.feat-eyebrow {
    text-align:center;
    font-size:7.5px;
    color:#8B6914;
    letter-spacing:4px;
    text-transform:uppercase;
    margin-bottom:{{ (int)($hPad * .35) }}px;
    position:relative;
}
.feat-eyebrow::before, .feat-eyebrow::after {
    content:'────────────';
    color:rgba(201,168,76,.4);
    font-size:6px;
    vertical-align:middle;
    margin:0 8px;
}

/* SECTION TABLE */
.sec-table { width:100%; border-collapse:separate; border-spacing:6px; }
.sec-cell {
    background:#FFFFFF;
    border-top:3px solid #D4AF37;
    border-left:1px solid rgba(201,168,76,.2);
    border-right:1px solid rgba(201,168,76,.2);
    border-bottom:1px solid rgba(201,168,76,.2);
    border-radius:0 0 8px 8px;
    padding:{{ $cPad }};
    vertical-align:top;
    width:50%;
}
.sec-title {
    font-size:{{ max(8, $iF - 1.5) }}px;
    font-weight:bold;
    color:#6B4A0A;
    letter-spacing:1.5px;
    text-transform:uppercase;
    padding-bottom:5px;
    margin-bottom:6px;
    border-bottom:1px dashed rgba(201,168,76,.35);
}
.sec-item {
    font-size:{{ $iF }}px;
    color:#2C1400;
    padding:{{ $iSp * .4 }}px 0 {{ $iSp * .4 }}px 13px;
    position:relative;
    line-height:1.4;
    border-bottom:1px solid rgba(201,168,76,.07);
}
.sec-item:last-child { border-bottom:none; }
.sec-item::before {
    content:'✓';
    color:#C9A84C;
    position:absolute;
    left:0;
    font-size:{{ $iF - 1.5 }}px;
    font-weight:bold;
}
.sec-empty { width:50%; }

/* ── FOOTER ── */
.footer {
    background:#1C0900;
    padding:10px 28px;
    position:absolute;
    bottom:4px;
    left:0; right:0;
}
.ft-t { width:100%; border-collapse:collapse; }
.ft-t td { vertical-align:middle; }
.ft-cta { font-size:11px; color:#D4AF37; font-weight:bold; letter-spacing:.5px; }
.ft-sub { font-size:7px; color:rgba(255,255,255,.35); margin-top:2px; }
.ft-ct  { font-size:7.5px; color:rgba(255,255,255,.45); text-align:right; line-height:1.8; }
.ft-ct strong { color:rgba(212,175,55,.75); }

/* ── BOTTOM STRIPE ── */
.stripe-bot {
    height:4px;
    background:#D4AF37;
    border-top:2px solid #8B6914;
    position:absolute;
    bottom:0; left:0; right:0;
}
</style>
</head>
<body>
<div style="width:210mm;height:297mm;background:#FAFAF7;position:relative;overflow:hidden;">

    <div class="stripe-top"></div>

    {{-- HEADER --}}
    <div class="header">
        <table class="hd-t">
            <tr>
                <td>
                    <div class="hd-brand">Anggita Wedding Organizer</div>
                    <div class="hd-sub">Surabaya &nbsp;·&nbsp; Professional Wedding Services</div>
                </td>
                <td>
                    <div class="hd-ct">
                        anggitaweddingsurabaya@gmail.com &nbsp;|&nbsp; @anggitawedding &nbsp;|&nbsp; anggitaweddingsby.com
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- HERO --}}
    <div class="hero">
        <div><span class="cat-pill">{{ $package->category_label }}</span></div>
        <div class="pkg-name">{{ $package->name }}</div>
        @if($package->tier)
            @php $tc = match($package->tier){ 'silver'=>'t-silver','gold'=>'t-gold',default=>'t-prem' }; @endphp
            <div><span class="tier-badge {{ $tc }}">&#10022; {{ ucfirst($package->tier) }} &#10022;</span></div>
        @endif

        <div class="price-box">
            @if($package->hasActivePromo())
                <div class="promo-tag">&#9889; {{ $package->promo_label ?? 'Promo Spesial' }}</div>
                <div class="p-label">Harga Normal</div>
                <div class="p-strike">{{ $package->formatted_price }}</div>
                <div class="p-label" style="margin-top:4px;">Harga Promo</div>
                <div class="p-main">{{ $package->formattedEffectivePrice }}</div>
            @else
                <div class="p-label">Mulai Dari</div>
                <div class="p-main">{{ $package->formatted_price }}</div>
            @endif
        </div>

    </div>

    {{-- META --}}
    @if($package->description || $package->has_digital_invitation)
    <div class="meta">
        @if($package->description)
            <div class="desc-txt">{{ $package->description }}</div>
        @endif
        @if($package->has_digital_invitation)
            <span class="inv-badge">&#9993;&nbsp; Termasuk Undangan Digital Premium</span>
        @endif
    </div>
    @endif

    {{-- FEATURES — always 2 columns --}}
    @if(!empty($sections))
    <div class="feat-area">
        <div class="feat-eyebrow">Apa Yang Anda Dapatkan</div>

        @if(count($sections) === 1 && !$sections[0]['title'])
            {{-- Flat list: 2 columns --}}
            @php $halves = array_chunk($sections[0]['items'], (int)ceil(count($sections[0]['items'])/2)); @endphp
            <table class="sec-table">
                <tr>
                    @foreach($halves as $half)
                    <td class="sec-cell">
                        @foreach($half as $item)
                            <div class="sec-item">{{ $item }}</div>
                        @endforeach
                    </td>
                    @endforeach
                    @if(count($halves) < 2)<td class="sec-empty"></td>@endif
                </tr>
            </table>
        @else
            {{-- Sectioned: rows of 2 --}}
            <table class="sec-table">
                @foreach($rowPairs as $pair)
                <tr>
                    @foreach($pair as $sec)
                    <td class="sec-cell">
                        @if($sec['title'])
                            <div class="sec-title">{{ $sec['title'] }}</div>
                        @endif
                        @foreach($sec['items'] as $item)
                            <div class="sec-item">{{ $item }}</div>
                        @endforeach
                    </td>
                    @endforeach
                    @if(count($pair) < 2)
                        <td class="sec-empty"></td>
                    @endif
                </tr>
                @endforeach
            </table>
        @endif
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        <table class="ft-t">
            <tr>
                <td>
                    <div class="ft-cta">Hubungi Kami Sekarang</div>
                    <div class="ft-sub">Konsultasi gratis &nbsp;&#183;&nbsp; Layanan Profesional &nbsp;&#183;&nbsp; Garansi kepuasan penuh</div>
                </td>

                <td>
                    <div class="ft-ct">
                        <strong>WA:</strong> +62 812-3456-7890 &nbsp;&nbsp;
                        <strong>IG:</strong> @anggitawedding &nbsp;&nbsp;
                        <strong>Web:</strong> anggitaweddingsby.com
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="stripe-bot"></div>
</div>
</body>
</html>
