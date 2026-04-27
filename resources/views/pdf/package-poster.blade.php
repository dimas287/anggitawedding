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
    
    // Ukuran diperbesar signifikan agar sangat jelas dibaca di layar HP
    // Adaptive Engine yang sudah di-tuning agar muat lebih banyak konten
    if ($weight <= 20) {
        $fItem = 13; $pCell = 12; $sItem = 5; $fSecTitle = 15;
    } elseif ($weight <= 40) {
        $fItem = 11.5; $pCell = 10; $sItem = 4; $fSecTitle = 13;
    } elseif ($weight <= 60) {
        $fItem = 10; $pCell = 8; $sItem = 3; $fSecTitle = 11.5;
    } elseif ($weight <= 80) {
        $fItem = 9; $pCell = 6;  $sItem = 2; $fSecTitle = 10.5;
    } else {
        $fItem = 8; $pCell = 4;  $sItem = 1.5; $fSecTitle = 9.5;
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
    $heroHeight = $hasPromo ? 205 : 170;
    $contentTop = $hasPromo ? 230 : 195;
    $contentHeight = $hasPromo ? 660 : 695;
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
    position: absolute; top: 10pt; left: 10pt; right: 10pt; bottom: 10pt;
    border: 1pt solid #C5A059;
    z-index: 100;
}
.frame-inner {
    position: absolute; top: 14pt; left: 14pt; right: 14pt; bottom: 14pt;
    border: 0.5pt solid rgba(197,160,89,0.5);
    z-index: 100;
}

/* ── HERO ── */
.hero {
    position: absolute;
    top: 14pt; left: 14pt; right: 14pt;
    height: {{ $heroHeight }}pt;
    background: #111111;
    color: #FFFFFF;
    text-align: center;
    padding-top: 15pt;
    border-bottom: 2pt solid #C5A059;
}

.brand-title { font-size: 13pt; color: #C5A059; letter-spacing: 3pt; font-weight: bold; }
.brand-sub { font-size: 7.5pt; color: #888888; letter-spacing: 2pt; margin-top: 2pt; margin-bottom: 8pt; }
.pkg-name { font-family: 'DejaVu Serif', 'Times New Roman', serif; font-size: {{ $fHeroName }}pt; font-weight: bold; color: #FFFFFF; letter-spacing: 1.5pt; margin-bottom: 5pt; }

.tier-badge {
    display: inline-block; padding: 3pt 16pt; border-radius: 15pt;
    font-size: 9pt; font-weight: bold; letter-spacing: 2pt;
    background: rgba(197,160,89,0.15); color: #E8C84A; border: 1pt solid rgba(197,160,89,0.4);
    margin-bottom: 8pt;
}

.price-container {
    display: inline-block; padding: 6pt 30pt;
    border-top: 1pt solid rgba(197,160,89,0.3); border-bottom: 1pt solid rgba(197,160,89,0.3);
}
.p-label { font-size: 8pt; color: #C5A059; letter-spacing: 2pt; margin-bottom: 2pt; }
.p-strike { font-size: 11pt; color: #888888; text-decoration: line-through; }
.p-main { font-family: 'DejaVu Serif', 'Times New Roman', serif; font-size: {{ $fHeroPrice }}pt; font-weight: bold; color: #E8C84A; line-height: 1; margin-top: 2pt; }

.promo-tag {
    font-size: 9pt; color: #E8C84A; letter-spacing: 2pt;
    text-transform: uppercase; margin-bottom: 2pt;
}

/* ── CONTENT AREA ── */
.content-area {
    position: absolute;
    top: {{ $contentTop }}pt; left: 24pt; right: 24pt;
    height: {{ $contentHeight }}pt; /* Area pasti untuk fitur */
    overflow: hidden; /* Potong jika kelebihan, cegah halaman baru */
    padding-top: 5pt;
}

.desc-txt {
    text-align: center;
    font-size: 11pt;
    color: #444444; font-style: italic; font-weight: bold; line-height: 1.3;
    margin-bottom: 8pt;
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
    margin-bottom: 5pt;
    box-shadow: 0 2pt 5pt rgba(0,0,0,0.02);
}

.card-title {
    font-size: {{ $fSecTitle }}pt; font-weight: bold; color: #1A1A1A;
    letter-spacing: 1pt; text-transform: uppercase; margin-bottom: 3pt;
    border-bottom: 1pt dashed rgba(197,160,89,0.3); padding-bottom: 2pt;
}

.feat-item {
    font-size: {{ $fItem }}pt; color: #222222;
    padding: {{ $sItem }}pt 0 {{ $sItem }}pt 10pt;
    position: relative; line-height: 1.1;
}
.feat-item::before {
    content: '◆'; color: #C5A059; position: absolute; left: 0; top: {{ $sItem }}pt;
    font-size: {{ max(5, $fItem - 2) }}pt;
}

.footer {
    position: absolute;
    bottom: 14pt; left: 14pt; right: 14pt;
    height: 55pt;
    background: #111111;
    border-top: 2pt solid #C5A059;
    text-align: center;
    padding-top: 8pt;
}

.ft-cta { font-size: 12pt; color: #C5A059; letter-spacing: 2pt; font-weight: bold; margin-bottom: 2pt; }
.ft-contact { font-size: 8pt; color: #888888; letter-spacing: 1.5pt; line-height: 1.4; font-weight: bold; }
.ft-contact strong { color: #D4AF37; }

</style>
</head>
<body>

<div class="frame-outer"></div>
<div class="frame-inner"></div>

<div class="hero">
    <div class="brand-title">Anggita Wedding Organizer</div>
    <div class="brand-sub">Professional Wedding Services</div>

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
                        @if(str_starts_with($item, '## '))
                            <div style="font-size: {{ $fItem - 0.5 }}pt; font-weight: bold; color: #666666; text-transform: uppercase; margin-top: 5pt; margin-bottom: 2pt; border-bottom: 0.5pt solid rgba(197,160,89,0.2); padding-bottom: 1pt;">
                                {{ ltrim(substr($item, 3)) }}
                            </div>
                        @else
                            <div class="feat-item">{{ $item }}</div>
                        @endif
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
        <strong>WA:</strong> +62 812-3112-2057 &nbsp; | &nbsp; <strong>IG:</strong> @anggita_wedding &nbsp; | &nbsp; <strong>WEB:</strong> anggitaweddingsby.com <br>
        <strong>ALAMAT:</strong> Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya
    </div>
</div>

</body>
</html>
