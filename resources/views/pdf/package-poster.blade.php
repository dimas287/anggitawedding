<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
@php
    /* ══════ PRECISION ADAPTIVE ENGINE v3 ══════ */
    $sections     = $package->feature_sections;
    $totalItems   = collect($sections)->sum(fn($s) => count($s['items'] ?? []));
    $numSections  = count($sections);
    $weight       = $totalItems + ($numSections * 3);

    if ($weight <= 15) {
        $fItem = 14; $pCell = 20; $sItem = 8; $fSecTitle = 16;
    } elseif ($weight <= 30) {
        $fItem = 12; $pCell = 15; $sItem = 6; $fSecTitle = 14;
    } elseif ($weight <= 45) {
        $fItem = 10; $pCell = 12; $sItem = 4; $fSecTitle = 12;
    } elseif ($weight <= 60) {
        $fItem = 9;  $pCell = 10; $sItem = 3; $fSecTitle = 11;
    } else {
        $fItem = 7.5;$pCell = 8;  $sItem = 2; $fSecTitle = 9;
    }

    /* 
     * THE SECRET TO 1 PAGE: 
     * Separate into 2 vertical columns instead of rows. 
     * This prevents DomPDF from breaking long rows to a new page!
     */
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
    font-family: 'Helvetica', Arial, sans-serif;
    width: 540pt; height: 960pt;
    margin: 0; padding: 0;
    background: #FDFCF8; /* Warm elegant paper color */
    color: #1A1A1A;
    overflow: hidden;
}

/* ── ARTISTIC FRAMES ── */
.frame-outer {
    position: absolute; top: 12pt; left: 12pt; right: 12pt; bottom: 12pt;
    border: 1.5pt solid #C5A059;
    z-index: 100; pointer-events: none;
}
.frame-inner {
    position: absolute; top: 16pt; left: 16pt; right: 16pt; bottom: 16pt;
    border: 0.5pt solid rgba(197,160,89,0.5);
    z-index: 100; pointer-events: none;
}
.corner { position: absolute; width: 15pt; height: 15pt; border: 2pt solid #C5A059; z-index: 101; }
.c-tl { top: 8pt; left: 8pt; border-bottom: none; border-right: none; }
.c-tr { top: 8pt; right: 8pt; border-bottom: none; border-left: none; }
.c-bl { bottom: 8pt; left: 8pt; border-top: none; border-right: none; }
.c-br { bottom: 8pt; right: 8pt; border-top: none; border-left: none; }

/* ── HERO & HEADER (LUXURY DARK) ── */
.hero {
    background: #111111;
    color: #FFFFFF;
    text-align: center;
    padding: 40pt 30pt 35pt;
    border-bottom: 3pt solid #C5A059;
    position: relative;
}

.brand-title {
    font-family: 'Times-Roman', serif;
    font-size: 18pt;
    color: #C5A059;
    letter-spacing: 5pt;
    text-transform: uppercase;
}

.brand-sub {
    font-size: 7.5pt;
    color: #888888;
    letter-spacing: 3pt;
    text-transform: uppercase;
    margin-top: 4pt;
    margin-bottom: 20pt;
}

.ornament {
    font-family: 'Times-Roman', serif;
    font-size: 14pt;
    color: rgba(197,160,89,0.5);
    margin: 15pt 0;
}

.pkg-name {
    font-family: 'Times-Roman', serif;
    font-size: 38pt;
    color: #FFFFFF;
    text-transform: uppercase;
    letter-spacing: 2pt;
    margin-bottom: 10pt;
}

.tier-badge {
    display: inline-block;
    padding: 4pt 24pt;
    border-radius: 20pt;
    font-size: 10pt;
    font-weight: bold;
    letter-spacing: 3pt;
    text-transform: uppercase;
    background: rgba(197,160,89,0.1);
    color: #C5A059;
    border: 1pt solid rgba(197,160,89,0.4);
    margin-bottom: 25pt;
}

.price-container {
    display: inline-block;
    padding: 10pt 40pt;
    border-top: 1pt solid rgba(197,160,89,0.3);
    border-bottom: 1pt solid rgba(197,160,89,0.3);
}

.p-label {
    font-size: 9pt;
    color: #C5A059;
    letter-spacing: 3pt;
    text-transform: uppercase;
    margin-bottom: 5pt;
}

.p-strike {
    font-size: 12pt;
    color: #666666;
    text-decoration: line-through;
}

.p-main {
    font-family: 'Times-Roman', serif;
    font-size: 46pt;
    color: #E8C84A;
    line-height: 1;
}

.promo-tag {
    display: inline-block;
    background: #E83A65;
    color: #FFFFFF;
    font-size: 8pt;
    font-weight: bold;
    letter-spacing: 2pt;
    text-transform: uppercase;
    padding: 3pt 12pt;
    border-radius: 10pt;
    margin-bottom: 8pt;
}

/* ── CONTENT AREA ── */
.content-area {
    padding: 25pt 35pt;
}

.desc-txt {
    text-align: center;
    font-family: 'Times-Roman', serif;
    font-size: {{ max(10, $fItem + 2) }}pt;
    color: #555555;
    font-style: italic;
    line-height: 1.6;
    margin-bottom: 20pt;
}

/* ── INDEPENDENT COLUMNS ── */
.main-table { width: 100%; border-collapse: separate; border-spacing: 15pt 0; margin-left: -7.5pt; }
.col-td { width: 50%; vertical-align: top; }

.card {
    background: #FFFFFF;
    border: 1pt solid rgba(197,160,89,0.3);
    border-top: 3pt solid #C5A059;
    border-radius: 4pt;
    padding: {{ $pCell }}pt;
    margin-bottom: 15pt;
    box-shadow: 0 5pt 15pt rgba(0,0,0,0.03);
}

.card-title {
    font-family: 'Times-Roman', serif;
    font-size: {{ $fSecTitle }}pt;
    font-weight: bold;
    color: #1A1A1A;
    letter-spacing: 1.5pt;
    text-transform: uppercase;
    margin-bottom: 10pt;
    border-bottom: 1pt solid rgba(197,160,89,0.2);
    padding-bottom: 6pt;
}

.feat-item {
    font-size: {{ $fItem }}pt;
    color: #333333;
    padding: {{ $sItem }}pt 0 {{ $sItem }}pt 14pt;
    position: relative;
    line-height: 1.4;
}

.feat-item::before {
    content: '❖'; /* Elegant diamond bullet */
    color: #C5A059;
    position: absolute;
    left: 0;
    top: {{ $sItem }}pt;
    font-size: {{ $fItem - 1 }}pt;
}

/* ── FOOTER ── */
.footer {
    position: absolute;
    bottom: 25pt;
    left: 35pt;
    right: 35pt;
    text-align: center;
    border-top: 1pt solid rgba(197,160,89,0.3);
    padding-top: 15pt;
}

.ft-cta {
    font-family: 'Times-Roman', serif;
    font-size: 14pt;
    color: #1A1A1A;
    letter-spacing: 2pt;
    text-transform: uppercase;
    font-weight: bold;
    margin-bottom: 6pt;
}

.ft-contact {
    font-size: 8.5pt;
    color: #555555;
    letter-spacing: 1pt;
}

.ft-contact strong { color: #C5A059; }

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

    <div class="ornament">~ ❖ ~</div>

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
