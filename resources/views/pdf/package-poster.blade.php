<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
@php
    /* ══════ PRECISION ADAPTIVE ENGINE ══════ */
    $sections     = $package->feature_sections;
    $totalItems   = collect($sections)->sum(fn($s) => count($s['items'] ?? []));
    $numSections  = count($sections);

    /* Weight = total items + (sections * 3) since headers take space */
    $weight = $totalItems + ($numSections * 3);

    /* Dynamic Sizing for 540x960pt (9:16) */
    if ($weight <= 15) {
        $fTitle=42; $fPrice=56; $fSecTitle=16; $fItem=14;   $pCell=20; $sItem=8;
    } elseif ($weight <= 30) {
        $fTitle=38; $fPrice=50; $fSecTitle=14; $fItem=12;   $pCell=16; $sItem=6;
    } elseif ($weight <= 45) {
        $fTitle=34; $fPrice=44; $fSecTitle=12; $fItem=10.5; $pCell=12; $sItem=4;
    } elseif ($weight <= 60) {
        $fTitle=30; $fPrice=38; $fSecTitle=11; $fItem=9;    $pCell=10; $sItem=3;
    } else {
        $fTitle=26; $fPrice=34; $fSecTitle=10; $fItem=8;    $pCell=8;  $sItem=2;
    }

    /* Group into rows of 2 */
    $rowPairs = array_chunk($sections, 2);
@endphp

<style>
@page { 
    margin: 0; 
    size: 540pt 960pt; /* Perfect 9:16 aspect ratio in points */
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    width: 540pt;
    height: 960pt;
    background: #F8F8F8;
    overflow: hidden;
    color: #222222;
}

.poster-wrapper {
    width: 540pt;
    height: 960pt;
    position: relative;
    background: #FBFBF9;
}

/* ── TOP SECTION (DARK LUXURY) ── */
.top-section {
    background: #141414;
    color: #FFFFFF;
    text-align: center;
    padding: 30pt 30pt 25pt;
    border-bottom: 4pt solid #D4AF37;
}

.brand {
    font-size: 14pt;
    color: #D4AF37;
    letter-spacing: 4pt;
    text-transform: uppercase;
    font-weight: bold;
    margin-bottom: 2pt;
}

.brand-sub {
    font-size: 7.5pt;
    color: #888888;
    letter-spacing: 2pt;
    text-transform: uppercase;
    margin-bottom: 25pt;
}

.cat-pill {
    display: inline-block;
    border: 1pt solid #D4AF37;
    border-radius: 20pt;
    padding: 4pt 16pt;
    font-size: 9pt;
    color: #D4AF37;
    letter-spacing: 2pt;
    text-transform: uppercase;
    margin-bottom: 12pt;
}

.pkg-name {
    font-size: {{ $fTitle }}pt;
    font-weight: bold;
    line-height: 1.1;
    margin-bottom: 12pt;
    text-transform: uppercase;
    letter-spacing: 1pt;
}

.tier-badge {
    display: inline-block;
    padding: 4pt 20pt;
    border-radius: 20pt;
    font-size: 10pt;
    font-weight: bold;
    letter-spacing: 2pt;
    text-transform: uppercase;
    background: #2A2A2A;
    color: #DDDDDD;
}
.t-gold { background: #D4AF37; color: #111111; }
.t-silver { background: #E0E0E0; color: #111111; }

.price-box {
    margin-top: 15pt;
    display: inline-block;
    border: 1pt solid rgba(212,175,55,0.4);
    border-radius: 12pt;
    padding: 12pt 30pt;
    background: rgba(212,175,55,0.05);
}

.p-label {
    font-size: 8pt;
    color: #D4AF37;
    letter-spacing: 2pt;
    text-transform: uppercase;
    margin-bottom: 4pt;
}

.p-strike {
    font-size: 12pt;
    color: #777777;
    text-decoration: line-through;
}

.p-main {
    font-size: {{ $fPrice }}pt;
    font-weight: bold;
    color: #E8C84A;
    line-height: 1;
}

.promo-tag {
    display: inline-block;
    background: #E83A65;
    color: #FFFFFF;
    font-size: 8pt;
    font-weight: bold;
    letter-spacing: 1pt;
    text-transform: uppercase;
    padding: 3pt 10pt;
    border-radius: 15pt;
    margin-bottom: 6pt;
}

/* ── CONTENT SECTION ── */
.content-section {
    padding: 20pt 30pt;
}

.meta-desc {
    text-align: center;
    margin-bottom: 15pt;
}

.desc-txt {
    font-size: {{ max(9, $fItem + 1) }}pt;
    color: #444444;
    line-height: 1.5;
    font-style: italic;
}

.inv-badge {
    display: inline-block;
    margin-top: 8pt;
    background: #FFFBEB;
    border: 1pt solid #D4AF37;
    border-radius: 6pt;
    padding: 5pt 12pt;
    font-size: 8.5pt;
    font-weight: bold;
    color: #8B6914;
    letter-spacing: 1pt;
    text-transform: uppercase;
}

.eyebrow {
    text-align: center;
    font-size: 10pt;
    color: #8B6914;
    letter-spacing: 4pt;
    text-transform: uppercase;
    margin-bottom: 15pt;
    border-bottom: 1px solid rgba(212,175,55,0.2);
    padding-bottom: 8pt;
}

/* ── FEATURES TABLE ── */
.sec-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 10pt;
    margin-left: -5pt; /* Compensate for spacing */
}

.sec-cell {
    background: #FFFFFF;
    border: 1pt solid #EAEAEA;
    border-top: 3pt solid #D4AF37;
    border-radius: 6pt;
    padding: {{ $pCell }}pt;
    vertical-align: top;
    width: 50%;
    box-shadow: 0 4pt 10pt rgba(0,0,0,0.02);
}

.sec-title {
    font-size: {{ $fSecTitle }}pt;
    font-weight: bold;
    color: #111111;
    letter-spacing: 1pt;
    text-transform: uppercase;
    margin-bottom: 8pt;
    border-bottom: 1pt dashed #EAEAEA;
    padding-bottom: 6pt;
}

.sec-item {
    font-size: {{ $fItem }}pt;
    color: #222222;
    padding: {{ $sItem }}pt 0 {{ $sItem }}pt 12pt;
    position: relative;
    line-height: 1.3;
}

.sec-item::before {
    content: '✔';
    color: #D4AF37;
    position: absolute;
    left: 0;
    top: {{ $sItem }}pt;
    font-size: {{ $fItem }}pt;
}

/* ── FOOTER ── */
.footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: #141414;
    padding: 15pt 30pt;
    border-top: 4pt solid #D4AF37;
}

.ft-table {
    width: 100%;
    border-collapse: collapse;
}

.ft-cta {
    font-size: 13pt;
    color: #D4AF37;
    font-weight: bold;
    letter-spacing: 1pt;
    text-transform: uppercase;
}

.ft-sub {
    font-size: 8pt;
    color: #888888;
    margin-top: 2pt;
}

.ft-ct {
    font-size: 8pt;
    color: #AAAAAA;
    text-align: right;
    line-height: 1.6;
}

.ft-ct strong {
    color: #D4AF37;
}

</style>
</head>
<body>

<div class="poster-wrapper">

    {{-- TOP SECTION --}}
    <div class="top-section">
        <div class="brand">Anggita Wedding Organizer</div>
        <div class="brand-sub">Surabaya &nbsp;·&nbsp; Professional Wedding Services</div>
        
        <div><span class="cat-pill">{{ $package->category_label }}</span></div>
        <div class="pkg-name">{{ $package->name }}</div>
        
        @if($package->tier)
            @php $tc = match($package->tier){ 'silver'=>'t-silver','gold'=>'t-gold',default=>'' }; @endphp
            <div><span class="tier-badge {{ $tc }}">&#10022; {{ ucfirst($package->tier) }} &#10022;</span></div>
        @endif

        <div class="price-box">
            @if($package->hasActivePromo())
                <div class="promo-tag">&#9889; {{ $package->promo_label ?? 'Promo Spesial' }}</div>
                <div class="p-label">Harga Normal</div>
                <div class="p-strike">{{ $package->formatted_price }}</div>
                <div class="p-label" style="margin-top:4pt;">Harga Promo</div>
                <div class="p-main">{{ $package->formattedEffectivePrice }}</div>
            @else
                <div class="p-label">Mulai Dari</div>
                <div class="p-main">{{ $package->formatted_price }}</div>
            @endif
        </div>
    </div>

    {{-- CONTENT SECTION --}}
    <div class="content-section">
        
        {{-- META --}}
        @if($package->description || $package->has_digital_invitation)
        <div class="meta-desc">
            @if($package->description)
                <div class="desc-txt">{{ $package->description }}</div>
            @endif
            @if($package->has_digital_invitation)
                <span class="inv-badge">&#9993;&nbsp; Termasuk Undangan Digital Premium</span>
            @endif
        </div>
        @endif

        {{-- FEATURES --}}
        @if(!empty($sections))
            <div class="eyebrow">Apa Yang Anda Dapatkan</div>

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
                        @if(count($halves) < 2)<td style="width:50%;"></td>@endif
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
                        @if(count($pair) < 2)<td style="width:50%;"></td>@endif
                    </tr>
                    @endforeach
                </table>
            @endif
        @endif

    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <table class="ft-table">
            <tr>
                <td>
                    <div class="ft-cta">Hubungi Kami Sekarang</div>
                    <div class="ft-sub">Konsultasi gratis &nbsp;&#183;&nbsp; Layanan Profesional &nbsp;&#183;&nbsp; Garansi kepuasan penuh</div>
                </td>
                <td>
                    <div class="ft-ct">
                        <strong>WA:</strong> +62 812-3456-7890 <br>
                        <strong>IG:</strong> @anggitawedding <br>
                        <strong>Web:</strong> anggitaweddingsby.com
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>
