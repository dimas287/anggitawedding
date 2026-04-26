<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
@php
    /* ══════ PRECISION ADAPTIVE ENGINE v4 ══════ */
    $sections     = $package->feature_sections;
    $totalItems   = collect($sections)->sum(fn($s) => count($s['items'] ?? []));
    $numSections  = count($sections);
    $weight       = $totalItems + ($numSections * 3);

    // Hero Sizes - Make them much smaller to save space
    $fHeroName = 28; $fHeroPrice = 36; 
    
    // Content Sizes
    if ($weight <= 15) {
        $fItem = 13; $pCell = 15; $sItem = 6; $fSecTitle = 15;
    } elseif ($weight <= 30) {
        $fItem = 11; $pCell = 12; $sItem = 4; $fSecTitle = 13;
    } elseif ($weight <= 45) {
        $fItem = 10; $pCell = 10; $sItem = 3; $fSecTitle = 11;
    } elseif ($weight <= 60) {
        $fItem = 8.5; $pCell = 8;  $sItem = 2; $fSecTitle = 10;
    } else {
        $fItem = 7.5; $pCell = 6;  $sItem = 1.5; $fSecTitle = 9;
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
@endphp
<style>
@page { margin: 0; size: 540pt 960pt; }

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DejaVu Sans', Arial, sans-serif; /* MUST use DejaVu for symbols to avoid ? */
    width: 540pt; height: 960pt;
    margin: 0; padding: 0;
    background: #FDFCF8;
    color: #1A1A1A;
    overflow: hidden;
}

/* ── ARTISTIC FRAMES ── */
.frame-outer {
    position: absolute; top: 10pt; left: 10pt; right: 10pt; bottom: 10pt;
    border: 1.5pt solid #C5A059;
    z-index: 100; pointer-events: none;
}
.frame-inner {
    position: absolute; top: 14pt; left: 14pt; right: 14pt; bottom: 14pt;
    border: 0.5pt solid rgba(197,160,89,0.5);
    z-index: 100; pointer-events: none;
}
.corner { position: absolute; width: 12pt; height: 12pt; border: 1.5pt solid #C5A059; z-index: 101; }
.c-tl { top: 6pt; left: 6pt; border-bottom: none; border-right: none; }
.c-tr { top: 6pt; right: 6pt; border-bottom: none; border-left: none; }
.c-bl { bottom: 6pt; left: 6pt; border-top: none; border-right: none; }
.c-br { bottom: 6pt; right: 6pt; border-top: none; border-left: none; }

/* ── HERO & HEADER ── */
.hero {
    background: #111111;
    color: #FFFFFF;
    text-align: center;
    padding: 25pt 20pt 20pt; /* Reduced padding drastically */
    border-bottom: 2pt solid #C5A059;
    position: relative;
    height: 220pt; /* Force a max height for hero to prevent taking too much space */
}

.brand-title {
    font-size: 14pt;
    color: #C5A059;
    letter-spacing: 4pt;
    text-transform: uppercase;
    font-weight: bold;
}

.brand-sub {
    font-size: 7pt;
    color: #888888;
    letter-spacing: 2pt;
    text-transform: uppercase;
    margin-top: 2pt;
    margin-bottom: 12pt;
}

.ornament {
    font-size: 10pt;
    color: rgba(197,160,89,0.7);
    margin: 8pt 0;
}

.pkg-name {
    font-size: {{ $fHeroName }}pt;
    font-weight: bold;
    color: #FFFFFF;
    text-transform: uppercase;
    letter-spacing: 1.5pt;
    margin-bottom: 6pt;
}

.tier-badge {
    display: inline-block;
    padding: 3pt 16pt;
    border-radius: 15pt;
    font-size: 8.5pt;
    font-weight: bold;
    letter-spacing: 2pt;
    text-transform: uppercase;
    background: rgba(197,160,89,0.15);
    color: #E8C84A;
    border: 1pt solid rgba(197,160,89,0.4);
    margin-bottom: 12pt;
}

.price-container {
    display: inline-block;
    padding: 8pt 30pt;
    border-top: 1pt solid rgba(197,160,89,0.3);
    border-bottom: 1pt solid rgba(197,160,89,0.3);
}

.p-label {
    font-size: 8pt;
    color: #C5A059;
    letter-spacing: 2pt;
    text-transform: uppercase;
    margin-bottom: 3pt;
}

.p-strike {
    font-size: 10pt;
    color: #666666;
    text-decoration: line-through;
}

.p-main {
    font-size: {{ $fHeroPrice }}pt;
    font-weight: bold;
    color: #E8C84A;
    line-height: 1;
}

.promo-tag {
    display: inline-block;
    background: #E83A65;
    color: #FFFFFF;
    font-size: 7pt;
    font-weight: bold;
    letter-spacing: 1pt;
    text-transform: uppercase;
    padding: 2pt 8pt;
    border-radius: 8pt;
    margin-bottom: 4pt;
}

/* ── CONTENT AREA ── */
.content-area {
    padding: 15pt 25pt;
    height: 640pt; /* Restrict height strictly */
    overflow: hidden;
}

.desc-txt {
    text-align: center;
    font-size: {{ max(9, $fItem + 1) }}pt;
    color: #444444;
    font-style: italic;
    line-height: 1.4;
    margin-bottom: 15pt;
}

/* ── INDEPENDENT COLUMNS ── */
.main-table { width: 100%; border-collapse: separate; border-spacing: 10pt 0; margin-left: -5pt; }
.col-td { width: 50%; vertical-align: top; }

.card {
    background: #FFFFFF;
    border: 1pt solid rgba(197,160,89,0.25);
    border-top: 2.5pt solid #C5A059;
    border-radius: 4pt;
    padding: {{ $pCell }}pt;
    margin-bottom: 10pt;
    box-shadow: 0 3pt 8pt rgba(0,0,0,0.02);
}

.card-title {
    font-size: {{ $fSecTitle }}pt;
    font-weight: bold;
    color: #1A1A1A;
    letter-spacing: 1pt;
    text-transform: uppercase;
    margin-bottom: 6pt;
    border-bottom: 1pt dashed rgba(197,160,89,0.3);
    padding-bottom: 4pt;
}

.feat-item {
    font-size: {{ $fItem }}pt;
    color: #222222;
    padding: {{ $sItem }}pt 0 {{ $sItem }}pt 10pt;
    position: relative;
    line-height: 1.3;
}

.feat-item::before {
    content: '◆'; /* Safe character for DejaVu */
    color: #C5A059;
    position: absolute;
    left: 0;
    top: {{ $sItem }}pt;
    font-size: {{ max(6, $fItem - 2) }}pt;
}

/* ── FOOTER ── */
.footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 70pt;
    text-align: center;
    background: #111111;
    border-top: 2pt solid #C5A059;
    padding-top: 15pt;
}

.ft-cta {
    font-size: 12pt;
    color: #C5A059;
    letter-spacing: 2pt;
    text-transform: uppercase;
    font-weight: bold;
    margin-bottom: 4pt;
}

.ft-contact {
    font-size: 7.5pt;
    color: #888888;
    letter-spacing: 1pt;
}

.ft-contact strong { color: #D4AF37; }

</style>
</head>
<body>

<div class="frame-outer"></div>
<div class="frame-inner"></div>
<div class="corner c-tl"></div>
<div class="corner c-tr"></div>
<div class="corner c-bl"></div>
<div class="corner c-br"></div>

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
            <div class="promo-tag">Promo Spesial</div><br>
            <span class="p-label">Normal: <span class="p-strike">{{ $package->formatted_price }}</span></span><br>
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

    <table class="main-table">
        <tr>
            {{-- Kiri (Col 1) --}}
            <td class="col-td">
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
            </td>

            {{-- Kanan (Col 2) --}}
            <td class="col-td">
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
            </td>
        </tr>
    </table>

</div>

<div class="footer">
    <div class="ft-cta">Hubungi Kami Sekarang</div>
    <div class="ft-contact">
        <strong>WA:</strong> +62 812-3456-7890 &nbsp; | &nbsp; <strong>IG:</strong> @anggitawedding &nbsp; | &nbsp; <strong>WEB:</strong> anggitaweddingsby.com
    </div>
</div>

</body>
</html>
