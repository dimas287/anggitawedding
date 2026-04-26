<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
@php
    /* ══════ ABSOLUTE ADAPTIVE ENGINE v5 ══════ */
    $sections     = $package->feature_sections;
    $totalItems   = collect($sections)->sum(fn($s) => count($s['items'] ?? []));
    $numSections  = count($sections);
    
    // Bobot dihitung lebih ketat
    $weight = $totalItems + ($numSections * 4);

    $fHeroName = 26; $fHeroPrice = 34; 
    
    // Ukuran dipaksa lebih kecil agar pasti muat dalam 640pt
    if ($weight <= 15) {
        $fItem = 11; $pCell = 12; $sItem = 5; $fSecTitle = 13;
    } elseif ($weight <= 30) {
        $fItem = 9.5; $pCell = 10; $sItem = 4; $fSecTitle = 11.5;
    } elseif ($weight <= 45) {
        $fItem = 8.5; $pCell = 8; $sItem = 3; $fSecTitle = 10;
    } elseif ($weight <= 60) {
        $fItem = 7.5; $pCell = 6;  $sItem = 2; $fSecTitle = 9;
    } else {
        $fItem = 6.5; $pCell = 5;  $sItem = 1.5; $fSecTitle = 8;
    }

    $col1 = [];
    $col2 = [];
    foreach ($sections as $index => $sec) {
        if ($index % 2 == 0) {
            $col1[] = $sec;
        } else {
            $col2[] = $sec;
        }
    }

    $hasPromo = $package->hasActivePromo();
    $heroHeight = $hasPromo ? 230 : 200;
    $contentTop = $hasPromo ? 260 : 230;
    $contentHeight = $hasPromo ? 610 : 640;
@endphp
<style>
@page { margin: 0; size: 540pt 960pt; }

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    width: 540pt; height: 960pt;
    margin: 0; padding: 0;
    background: #FDFCF8;
    color: #1A1A1A;
}

/* 
 * KUNCI UTAMA (SILVER BULLET): 
 * Semua area utama di set Absolute. DomPDF tidak akan pernah 
 * bisa memecahnya ke halaman kedua karena posisinya dikunci mutlak.
 */

.frame-outer {
    position: absolute; top: 12pt; left: 12pt; right: 12pt; bottom: 12pt;
    border: 1pt solid #C5A059;
    z-index: 100;
}
.frame-inner {
    position: absolute; top: 16pt; left: 16pt; right: 16pt; bottom: 16pt;
    border: 0.5pt solid rgba(197,160,89,0.5);
    z-index: 100;
}

/* ── HERO ── */
.hero {
    position: absolute;
    top: 16pt; left: 16pt; right: 16pt;
    height: {{ $heroHeight }}pt;
    background: #111111;
    color: #FFFFFF;
    text-align: center;
    padding-top: 15pt;
    border-bottom: 2pt solid #C5A059;
}

.brand-title { font-size: 13pt; color: #C5A059; letter-spacing: 3pt; font-weight: bold; }
.brand-sub { font-size: 6.5pt; color: #888888; letter-spacing: 2pt; margin-top: 2pt; }
.ornament { font-size: 9pt; color: rgba(197,160,89,0.7); margin: 6pt 0; }
.pkg-name { font-family: 'DejaVu Serif', 'Times New Roman', serif; font-size: {{ $fHeroName }}pt; font-weight: bold; color: #FFFFFF; letter-spacing: 1.5pt; margin-bottom: 5pt; }

.tier-badge {
    display: inline-block; padding: 3pt 16pt; border-radius: 15pt;
    font-size: 8pt; font-weight: bold; letter-spacing: 2pt;
    background: rgba(197,160,89,0.15); color: #E8C84A; border: 1pt solid rgba(197,160,89,0.4);
    margin-bottom: 8pt;
}

.price-container {
    display: inline-block; padding: 6pt 30pt;
    border-top: 1pt solid rgba(197,160,89,0.3); border-bottom: 1pt solid rgba(197,160,89,0.3);
}
.p-label { font-size: 7pt; color: #C5A059; letter-spacing: 2pt; margin-bottom: 2pt; }
.p-strike { font-size: 10pt; color: #888888; text-decoration: line-through; }
.p-main { font-family: 'DejaVu Serif', 'Times New Roman', serif; font-size: {{ $fHeroPrice }}pt; font-weight: bold; color: #E8C84A; line-height: 1; margin-top: 2pt; }

.promo-tag {
    font-size: 8pt; color: #E8C84A; letter-spacing: 2pt;
    text-transform: uppercase; margin-bottom: 2pt;
}

/* ── CONTENT AREA ── */
.content-area {
    position: absolute;
    top: {{ $contentTop }}pt; left: 30pt; right: 30pt;
    height: {{ $contentHeight }}pt; /* Area pasti untuk fitur */
    overflow: hidden; /* Potong jika kelebihan, cegah halaman baru */
}

.desc-txt {
    text-align: center;
    font-size: {{ max(8, $fItem + 1) }}pt;
    color: #444444; font-style: italic; line-height: 1.4;
    margin-bottom: 15pt;
}

/* Float Layout pengganti Table untuk mencegah pemotongan baris DomPDF */
.col-wrapper { width: 100%; }
.col-left { float: left; width: 48%; }
.col-right { float: right; width: 48%; }
.clearfix { clear: both; }

.card {
    background: #FFFFFF;
    border: 1pt solid rgba(197,160,89,0.25);
    border-top: 2.5pt solid #C5A059;
    border-radius: 4pt;
    padding: {{ $pCell }}pt;
    margin-bottom: 10pt;
    box-shadow: 0 2pt 5pt rgba(0,0,0,0.02);
}

.card-title {
    font-size: {{ $fSecTitle }}pt; font-weight: bold; color: #1A1A1A;
    letter-spacing: 1pt; text-transform: uppercase; margin-bottom: 6pt;
    border-bottom: 1pt dashed rgba(197,160,89,0.3); padding-bottom: 4pt;
}

.feat-item {
    font-size: {{ $fItem }}pt; color: #222222;
    padding: {{ $sItem }}pt 0 {{ $sItem }}pt 10pt;
    position: relative; line-height: 1.3;
}
.feat-item::before {
    content: '◆'; color: #C5A059; position: absolute; left: 0; top: {{ $sItem }}pt;
    font-size: {{ max(5, $fItem - 2) }}pt;
}

.footer {
    position: absolute;
    bottom: 16pt; left: 16pt; right: 16pt;
    height: 60pt;
    background: #111111;
    border-top: 2pt solid #C5A059;
    text-align: center;
    padding-top: 10pt;
}

.ft-cta { font-size: 11pt; color: #C5A059; letter-spacing: 2pt; font-weight: bold; margin-bottom: 2pt; }
.ft-contact { font-size: 6pt; color: #888888; letter-spacing: 1.5pt; line-height: 1.4; }
.ft-contact strong { color: #D4AF37; }

</style>
</head>
<body>

<div class="frame-outer"></div>
<div class="frame-inner"></div>

<div class="hero">
    <div class="brand-title">Anggita Wedding Organizer</div>
    <div class="brand-sub">Professional Wedding Services</div>

    <div class="ornament">&#10022; &nbsp; &#9672; &nbsp; &#10022;</div>

    <div class="pkg-name">{{ $package->name }}</div>
    
    @if($package->tier)
        <div><span class="tier-badge">{{ $package->tier }}</span></div>
    @endif

    <div class="price-container">
        @if($package->hasActivePromo())
            <div class="promo-tag">&mdash; Special Promo {{ round($package->promo_discount_percent) }}% OFF &mdash;</div>
            <div style="margin-bottom: 2pt;">
                <span style="font-size: 9pt; color: #666666; font-style: italic;">Normal: </span>
                <span class="p-strike">{{ $package->formatted_price }}</span>
            </div>
            <div class="p-main">{{ $package->formattedEffectivePrice }}</div>
        @else
            <div class="p-label">Mulai Dari</div>
            <div class="p-main">{{ $package->formatted_price }}</div>
        @endif
    </div>
</div>

<div class="content-area">
    @if($package->description)
        <div class="desc-txt">"{{ $package->description }}"</div>
    @endif

    <div class="col-wrapper">
        <div class="col-left">
            @foreach($col1 as $sec)
                <div class="card">
                    @if($sec['title'])
                        <div class="card-title">{{ $sec['title'] }}</div>
                    @endif
                    @foreach($sec['items'] as $item)
                        <div class="feat-item">{{ $item }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="col-right">
            @foreach($col2 as $sec)
                <div class="card">
                    @if($sec['title'])
                        <div class="card-title">{{ $sec['title'] }}</div>
                    @endif
                    @foreach($sec['items'] as $item)
                        <div class="feat-item">{{ $item }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<div class="footer">
    <div class="ft-cta">Hubungi Kami Sekarang</div>
    <div class="ft-contact">
        <strong>WA:</strong> +62 812-3112-2057 &nbsp; | &nbsp; <strong>IG:</strong> @anggita_wedding <br>
        <strong>ALAMAT:</strong> Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya
    </div>
</div>

</body>
</html>
