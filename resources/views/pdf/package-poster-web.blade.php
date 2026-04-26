<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Generating {{ strtoupper($format) }} Poster...</title>
@php
    /* ══════ ABSOLUTE ADAPTIVE ENGINE v5 ══════ */
    $sections     = $package->feature_sections;
    $totalItems   = collect($sections)->sum(fn($s) => count($s['items'] ?? []));
    $numSections  = count($sections);
    
    // Bobot dihitung lebih ketat
    $weight = $totalItems + ($numSections * 4);

    $fHeroName = 26; $fHeroPrice = 34; 
    
    // Ukuran dipaksa lebih kecil agar pasti muat dalam 640pt (tapi sudah dibesarkan +1.5pt untuk mobile)
    if ($weight <= 15) {
        $fItem = 12.5; $pCell = 14; $sItem = 6; $fSecTitle = 14.5;
    } elseif ($weight <= 30) {
        $fItem = 11; $pCell = 12; $sItem = 5; $fSecTitle = 13;
    } elseif ($weight <= 45) {
        $fItem = 10; $pCell = 10; $sItem = 4; $fSecTitle = 11.5;
    } elseif ($weight <= 60) {
        $fItem = 9; $pCell = 8;  $sItem = 3; $fSecTitle = 10.5;
    } else {
        $fItem = 8; $pCell = 6;  $sItem = 2.5; $fSecTitle = 9.5;
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
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    background: #e5e7eb;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

#poster-capture {
    width: 540pt; height: 960pt;
    background: #FDFCF8;
    color: #1A1A1A;
    position: relative;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    transform: scale(0.9);
    transform-origin: center;
}

.loading-overlay {
    position: fixed; inset: 0; background: rgba(255,255,255,0.9);
    display: flex; flex-direction: column; justify-content: center; align-items: center;
    z-index: 9999; font-family: sans-serif; color: #111;
}

.spinner {
    width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #C5A059;
    border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 15px;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

/* ── KUNCI UTAMA (SILVER BULLET) ── */
.frame-outer {
    position: absolute; top: 12pt; left: 12pt; right: 12pt; bottom: 12pt;
    border: 1pt solid #C5A059; z-index: 100; pointer-events: none;
}
.frame-inner {
    position: absolute; top: 16pt; left: 16pt; right: 16pt; bottom: 16pt;
    border: 0.5pt solid rgba(197,160,89,0.5); z-index: 100; pointer-events: none;
}

/* ── HERO ── */
.hero {
    position: absolute; top: 16pt; left: 16pt; right: 16pt;
    height: {{ $heroHeight }}pt; background: #111111; color: #FFFFFF;
    text-align: center; padding-top: 15pt; border-bottom: 2pt solid #C5A059;
}

.hero-logo {
    position: absolute;
    top: 15pt;
    left: 15pt;
    width: 45pt;
    height: auto;
}

.brand-title { font-size: 13pt; color: #C5A059; letter-spacing: 3pt; font-weight: bold; }
.brand-sub { font-size: 7.5pt; color: #888888; letter-spacing: 2pt; margin-top: 2pt; }
.ornament { font-size: 10pt; color: rgba(197,160,89,0.7); margin: 6pt 0; }
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
    position: absolute; top: {{ $contentTop }}pt; left: 30pt; right: 30pt;
    height: {{ $contentHeight }}pt; overflow: hidden;
}

.desc-txt {
    text-align: center; font-size: {{ max(8, $fItem + 1) }}pt;
    color: #444444; font-style: italic; line-height: 1.4; margin-bottom: 15pt;
}

.col-wrapper { width: 100%; }
.col-left { float: left; width: 48%; }
.col-right { float: right; width: 48%; }
.clearfix { clear: both; }

.card {
    background: #FFFFFF; border: 1pt solid rgba(197,160,89,0.25);
    border-top: 2.5pt solid #C5A059; border-radius: 4pt;
    padding: {{ $pCell }}pt; margin-bottom: 10pt; box-shadow: 0 2pt 5pt rgba(0,0,0,0.02);
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
    position: absolute; bottom: 16pt; left: 16pt; right: 16pt;
    height: 60pt; background: #111111; border-top: 2pt solid #C5A059;
    text-align: center; padding-top: 10pt;
}

.ft-cta { font-size: 11pt; color: #C5A059; letter-spacing: 2pt; font-weight: bold; margin-bottom: 2pt; }
.ft-contact { font-size: 7pt; color: #888888; letter-spacing: 1pt; line-height: 1.5; }
.ft-contact strong { color: #D4AF37; }

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <h2 style="margin: 0">Memproses {{ strtoupper($format) }}...</h2>
    <p style="color: #666; margin-top: 8px; font-size: 14px;">Tunggu sebentar, file akan otomatis di-download.</p>
</div>

<div id="poster-capture">
    <div class="frame-outer"></div>
    <div class="frame-inner"></div>

    <div class="hero">
        <img src="{{ asset('images/brand/anggita-logo-main.svg') }}" class="hero-logo" alt="Logo">
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
            <strong>WA:</strong> +62 812-3112-2057 &nbsp; | &nbsp; <strong>IG:</strong> @anggita_wedding &nbsp; | &nbsp; <strong>WEB:</strong> anggitaweddingsby.com <br>
            <strong>ALAMAT:</strong> Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya
        </div>
    </div>
</div>

<script>
window.onload = function() {
    // Beri waktu agar font sistem bisa dirender sempurna oleh browser
    setTimeout(() => {
        const captureArea = document.querySelector("#poster-capture");
        
        // Reset transform to fix the white space at the bottom issue in html2canvas
        captureArea.style.transform = "none";
        document.body.style.display = "block"; // Reset flex just to be safe
        document.body.style.minHeight = "auto";
        window.scrollTo(0,0);

        html2canvas(captureArea, {
            scale: 2, // High resolution (retina)
            useCORS: true,
            allowTaint: true,
            backgroundColor: "#FDFCF8",
            width: captureArea.offsetWidth,
            height: captureArea.offsetHeight,
            windowWidth: captureArea.offsetWidth,
            windowHeight: captureArea.offsetHeight,
            y: 0,
            x: 0
        }).then(canvas => {
            let link = document.createElement('a');
            link.download = 'Poster-{{ $package->slug }}.{{ $format }}';
            link.href = canvas.toDataURL('image/{{ $format === "jpg" ? "jpeg" : "png" }}', 0.95);
            link.click();
            
            document.getElementById('loadingOverlay').innerHTML = `
                <div style="text-align: center; color: #15803d;">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <h2 style="margin: 0; color: #111;">Berhasil!</h2>
                    <p style="color: #666; margin-top: 8px; font-size: 14px;">File {{ strtoupper($format) }} telah didownload.</p>
                    <p style="color: #999; margin-top: 4px; font-size: 12px;">Halaman ini bisa ditutup.</p>
                </div>
            `;
            
            // Opsional: Tutup tab otomatis
            // setTimeout(() => { window.close(); }, 3000);
        }).catch(err => {
            document.getElementById('loadingOverlay').innerHTML = `
                <div style="text-align: center; color: #dc2626;">
                    <h2 style="margin: 0;">Gagal</h2>
                    <p style="color: #666; margin-top: 8px; font-size: 14px;">Terjadi kesalahan saat membuat gambar.</p>
                </div>
            `;
            console.error('Error rendering image:', err);
        });
    }, 1000);
};
</script>
</body>
</html>
