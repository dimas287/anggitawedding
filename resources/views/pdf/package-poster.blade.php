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
        if      ($weight <= 12) { $tier='xl'; }
        elseif  ($weight <= 24) { $tier='lg'; }
        elseif  ($weight <= 38) { $tier='md'; }
        elseif  ($weight <= 52) { $tier='sm'; }
        elseif  ($weight <= 70) { $tier='xs'; }
        else                    { $tier='xxs'; }

        /* Always 2 columns for sections */
        $rowPairs = array_chunk($sections, 2);
    @endphp

<style>
@page { 
    margin: 0; 
    size: 1080px 1920px; /* Format 9:16 */
}
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    background: #FFFFFF; /* Putih bersih agar kontras maksimal */
    color: #000000; /* Hitam pekat agar terbaca jelas */
    width: 1080px;
    height: 1920px;
    overflow: hidden;
}

/* ── GOLD STRIPE ── */
.stripe-top {
    height: 15px;
    background: #D4AF37;
    border-bottom: 4px solid #8B6914;
}

/* ── HEADER ── */
.header {
    background: #111111;
    padding: 25px 50px;
}
.hd-t { width: 100%; border-collapse: collapse; }
.hd-t td { vertical-align: middle; }
.hd-brand { font-size: 24px; font-weight: bold; color: #D4AF37; letter-spacing: 4px; text-transform: uppercase; }
.hd-sub   { font-size: 14px; color: rgba(212,175,55,0.7); letter-spacing: 3px; text-transform: uppercase; margin-top: 5px; }
.hd-ct    { font-size: 14px; color: #888; text-align: right; line-height: 1.8; }

/* ── HERO ── */
.hero {
    background: #111111;
    text-align: center;
    padding: 60px 50px 50px;
    border-bottom: 6px solid #D4AF37;
    position: relative;
}
.cat-pill {
    display: inline-block;
    border: 2px solid #D4AF37;
    border-radius: 40px;
    padding: 5px 25px;
    font-size: 16px;
    color: #D4AF37;
    letter-spacing: 4px;
    text-transform: uppercase;
    margin-bottom: 20px;
}
.pkg-name {
    font-size: 72px; /* Lebih besar */
    font-weight: bold;
    color: #FFFFFF;
    line-height: 1.1;
    margin-bottom: 15px;
    letter-spacing: 1px;
}
.tier-badge {
    display: inline-block;
    padding: 6px 30px;
    border-radius: 40px;
    font-size: 18px;
    font-weight: bold;
    letter-spacing: 4px;
    text-transform: uppercase;
    margin-bottom: 30px;
}
.t-silver { background: #333; color: #EEE; border: 2px solid #666; }
.t-gold   { background: #D4AF37; color: #111; border: 2px solid #8B6914; }
.t-prem   { background: #7C3AED; color: #FFF; border: 2px solid #5B21B6; }

/* PRICE BOX */
.price-box {
    display: inline-block;
    border: 3px solid #D4AF37;
    border-radius: 20px;
    padding: 25px 60px;
    background: rgba(212,175,55,0.1);
}
.p-label { font-size: 16px; color: #D4AF37; letter-spacing: 4px; text-transform: uppercase; margin-bottom: 5px; }
.p-strike{ font-size: 24px; color: #666; text-decoration: line-through; }
.p-main  { font-size: 84px; font-weight: bold; color: #E8C84A; line-height: 1; margin-bottom: 10px; }
.promo-tag {
    display: inline-block;
    background: #EC4899;
    color: #FFF;
    font-size: 16px; font-weight: bold;
    letter-spacing: 2px; text-transform: uppercase;
    padding: 5px 20px; border-radius: 30px;
    margin-bottom: 10px;
}

/* ── META ── */
.meta {
    background: #F9F9F9;
    padding: 40px 50px;
    text-align: center;
    border-bottom: 2px solid #EEE;
}
.desc-txt { font-size: 22px; color: #333; line-height: 1.6; margin-bottom: 15px; font-weight: bold; }
.inv-badge {
    display: inline-block;
    background: #FFFBEB;
    border: 2px solid #D4AF37;
    border-radius: 10px;
    padding: 10px 30px;
    font-size: 18px; font-weight: bold;
    color: #8B6914; letter-spacing: 1px; text-transform: uppercase;
}

/* ── FEATURES AREA ── */
.feat-area { padding: 40px 40px; }
.feat-eyebrow {
    text-align: center;
    font-size: 18px;
    color: #8B6914;
    letter-spacing: 6px;
    text-transform: uppercase;
    margin-bottom: 30px;
}

/* SECTION TABLE */
.sec-table { width: 100%; border-collapse: separate; border-spacing: 15px; }
.sec-cell {
    background: #FFFFFF;
    border: 3px solid #EEE;
    border-top: 8px solid #D4AF37;
    border-radius: 15px;
    padding: 30px;
    vertical-align: top;
    width: 50%;
}
.sec-title {
    font-size: 20px;
    font-weight: bold;
    color: #111;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding-bottom: 10px;
    margin-bottom: 15px;
    border-bottom: 3px solid #F3F4F6;
}
.sec-item {
    font-size: 18px; /* Jauh lebih besar */
    color: #000;
    font-weight: bold;
    padding: 8px 0 8px 30px;
    position: relative;
    line-height: 1.4;
}
.sec-item::before {
    content: '✔';
    color: #D4AF37;
    position: absolute;
    left: 0;
    font-size: 22px;
}

/* ── FOOTER ── */
.footer {
    background: #111111;
    padding: 30px 50px;
    position: absolute;
    bottom: 15px;
    left: 0; right: 0;
}
.ft-t { width: 100%; border-collapse: collapse; }
.ft-cta { font-size: 24px; color: #D4AF37; font-weight: bold; letter-spacing: 1px; }
.ft-sub { font-size: 16px; color: #666; margin-top: 5px; }
.ft-ct  { font-size: 16px; color: #AAA; text-align: right; line-height: 1.8; }
.ft-ct strong { color: #D4AF37; }

.stripe-bot {
    height: 15px;
    background: #D4AF37;
    position: absolute;
    bottom: 0; left: 0; right: 0;
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
