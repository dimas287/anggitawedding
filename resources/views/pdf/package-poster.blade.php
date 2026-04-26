<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    @php
        /* ─── ADAPTIVE SIZING ENGINE ─── */
        $sections    = $package->feature_sections;
        $totalItems  = collect($sections)->sum(fn($s) => count($s['items'] ?? []));
        $sectionCount = count($sections);

        /* Choose tier based on content volume */
        $totalUnits = $totalItems + ($sectionCount * 2); // sections add heading weight

        if ($totalUnits <= 12) {
            $tier = 'xl';   // very few items → huge text fills page
        } elseif ($totalUnits <= 22) {
            $tier = 'lg';
        } elseif ($totalUnits <= 34) {
            $tier = 'md';
        } elseif ($totalUnits <= 50) {
            $tier = 'sm';
        } else {
            $tier = 'xs';
        }

        /* Font & spacing values per tier */
        $sizes = [
            'xl' => ['name'=>38, 'price'=>42, 'item'=>13, 'title'=>10, 'label'=>9,  'desc'=>12, 'pad'=>22, 'ipad'=>'8px 14px', 'sp'=>6,  'cols'=>2],
            'lg' => ['name'=>32, 'price'=>36, 'item'=>11, 'title'=>9,  'label'=>8,  'desc'=>10, 'pad'=>18, 'ipad'=>'7px 11px', 'sp'=>5,  'cols'=>2],
            'md' => ['name'=>26, 'price'=>30, 'item'=>9.5,'title'=>8,  'label'=>7.5,'desc'=>9,  'pad'=>14, 'ipad'=>'5px 9px',  'sp'=>3.5,'cols'=>3],
            'sm' => ['name'=>22, 'price'=>26, 'item'=>8,  'title'=>7.5,'label'=>7,  'desc'=>8,  'pad'=>10, 'ipad'=>'4px 8px',  'sp'=>2.5,'cols'=>3],
            'xs' => ['name'=>18, 'price'=>22, 'item'=>7,  'title'=>7,  'label'=>6.5,'desc'=>7.5,'pad'=>8,  'ipad'=>'3px 6px',  'sp'=>2,  'cols'=>3],
        ];
        $sz = $sizes[$tier];

        /* Column layout for sections */
        $cols = $sz['cols'];
    @endphp

    <style>
        @page { margin: 0; size: A4 portrait; }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #FDFAF4;
            color: #1C0E00;
            width: 210mm;
            height: 297mm;
            overflow: hidden;
        }

        .poster {
            width: 210mm;
            height: 297mm;
            background: #FDFAF4;
            position: relative;
        }

        /* ─── TOP STRIPE ─── */
        .top-stripe {
            height: 7px;
            background: linear-gradient(to right, #8B6914, #D4AF37, #F0D060, #D4AF37, #8B6914);
        }

        /* ─── HEADER ─── */
        .header {
            background: #1C0E00;
            padding: 10px {{ $sz['pad'] }}px;
        }
        .header-inner { width: 100%; border-collapse: collapse; }
        .header-inner td { vertical-align: middle; }
        .brand-name {
            font-size: 13px;
            font-weight: bold;
            color: #D4AF37;
            letter-spacing: 2.5px;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 7px;
            color: rgba(212,175,55,0.5);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .header-contact {
            font-size: 7px;
            color: rgba(255,255,255,0.45);
            text-align: right;
            line-height: 1.7;
        }

        /* ─── HERO BAND ─── */
        .hero-band {
            background: linear-gradient(135deg, #2C1505 0%, #3D1F06 40%, #2C1505 100%);
            padding: {{ $sz['pad'] }}px {{ $sz['pad'] }}px {{ (int)($sz['pad'] * 0.7) }}px;
            text-align: center;
            border-bottom: 2px solid #D4AF37;
            position: relative;
        }
        .hero-band::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
        }
        .category-pill {
            display: inline-block;
            border: 1px solid rgba(212,175,55,0.35);
            border-radius: 30px;
            padding: 2px 12px;
            font-size: 7px;
            color: rgba(212,175,55,0.7);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .package-name {
            font-size: {{ $sz['name'] }}px;
            font-weight: bold;
            color: #FFFFFF;
            line-height: 1.1;
            margin-bottom: 6px;
        }
        .tier-badge {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 2.5px;
            text-transform: uppercase;
        }
        .tier-silver { background: rgba(156,163,175,0.15); color: #C0C8D5; border: 1px solid rgba(200,210,220,0.3); }
        .tier-gold   { background: rgba(212,175,55,0.15);  color: #E8C84A; border: 1px solid rgba(212,175,55,0.4); }
        .tier-premium, .tier-platinum { background: rgba(167,139,250,0.15); color: #C4AFFE; border: 1px solid rgba(167,139,250,0.35); }

        /* ─── CONTENT AREA (cream background) ─── */
        .content {
            background: #FDFAF4;
            padding: {{ (int)($sz['pad'] * 0.8) }}px {{ $sz['pad'] }}px {{ (int)($sz['pad'] * 0.6) }}px;
        }

        /* ─── META ROW (price + desc) ─── */
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { vertical-align: top; }

        .price-block {
            background: linear-gradient(135deg, #2C1505, #3D1F06);
            border: 1px solid #D4AF37;
            border-radius: 10px;
            padding: {{ $sz['ipad'] }};
            text-align: center;
        }
        .price-label {
            font-size: {{ $sz['label'] }}px;
            color: rgba(212,175,55,0.7);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .price-original {
            font-size: {{ $sz['label'] + 1 }}px;
            color: rgba(255,255,255,0.35);
            text-decoration: line-through;
        }
        .price-main {
            font-size: {{ $sz['price'] }}px;
            font-weight: bold;
            color: #E8C84A;
            line-height: 1;
            margin-bottom: 4px;
        }
        .price-dp {
            font-size: {{ $sz['label'] }}px;
            color: rgba(255,255,255,0.5);
        }
        .price-dp strong { color: rgba(212,175,55,0.85); }
        .promo-tag {
            display: inline-block;
            background: rgba(236,72,153,0.2);
            border: 1px solid rgba(236,72,153,0.4);
            color: #F9A8D4;
            font-size: {{ $sz['label'] - 0.5 }}px;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 2px 9px;
            border-radius: 20px;
            margin-bottom: 4px;
        }

        .side-meta { padding-left: 14px; }
        .desc-text {
            font-size: {{ $sz['desc'] }}px;
            color: #5C3D15;
            line-height: 1.6;
            margin-bottom: 6px;
        }
        .invite-badge {
            display: inline-block;
            background: #FFF8E8;
            border: 1px solid #D4AF37;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: {{ $sz['label'] }}px;
            color: #8B6914;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* ─── GOLD ORNAMENT DIVIDER ─── */
        .ornament {
            text-align: center;
            color: #C9A84C;
            font-size: 11px;
            letter-spacing: 6px;
            margin: {{ (int)($sz['pad'] * 0.5) }}px 0 {{ (int)($sz['pad'] * 0.4) }}px;
        }
        .section-eyebrow {
            font-size: 7.5px;
            color: #8B6914;
            letter-spacing: 4px;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 8px;
        }

        /* ─── FEATURE SECTIONS ─── */
        .features-table { width: 100%; border-collapse: separate; border-spacing: 5px; }
        .feature-cell {
            background: #FFFFFF;
            border: 1px solid rgba(201,168,76,0.25);
            border-radius: 8px;
            padding: {{ $sz['ipad'] }};
            vertical-align: top;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .feature-cell-top {
            border-top: 3px solid #D4AF37;
            border-radius: 0 0 8px 8px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        .feature-cell-title {
            font-size: {{ $sz['title'] }}px;
            font-weight: bold;
            color: #8B6914;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 5px;
            padding-bottom: 4px;
            border-bottom: 1px solid rgba(201,168,76,0.3);
        }
        .feature-item {
            font-size: {{ $sz['item'] }}px;
            color: #3D2000;
            padding: {{ $sz['sp'] }}px 0;
            padding-left: 12px;
            position: relative;
            line-height: 1.35;
            border-bottom: 1px solid rgba(201,168,76,0.08);
        }
        .feature-item:last-child { border-bottom: none; }
        .feature-item::before {
            content: "✓";
            color: #C9A84C;
            position: absolute;
            left: 0;
            font-size: {{ $sz['item'] - 1 }}px;
            font-weight: bold;
        }

        /* ─── FLAT LIST ─── */
        .flat-table { width: 100%; border-collapse: separate; border-spacing: 5px; }
        .flat-cell {
            background: #FFFFFF;
            border: 1px solid rgba(201,168,76,0.2);
            border-left: 3px solid #D4AF37;
            border-radius: 0 6px 6px 0;
            padding: {{ $sz['sp'] }}px 10px;
            font-size: {{ $sz['item'] }}px;
            color: #3D2000;
            vertical-align: middle;
        }
        .flat-check { color: #C9A84C; margin-right: 4px; font-weight: bold; }

        /* ─── FOOTER ─── */
        .footer {
            background: #1C0E00;
            padding: 9px {{ $sz['pad'] }}px;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { vertical-align: middle; }
        .footer-cta { font-size: {{ $sz['label'] + 2 }}px; color: #E8C84A; font-weight: bold; letter-spacing: 0.5px; }
        .footer-sub { font-size: {{ $sz['label'] - 0.5 }}px; color: rgba(255,255,255,0.35); margin-top: 2px; }
        .footer-contacts { font-size: {{ $sz['label'] }}px; color: rgba(255,255,255,0.45); text-align: right; line-height: 1.8; }
        .footer-contacts strong { color: rgba(212,175,55,0.75); }

        /* ─── BOTTOM STRIPE ─── */
        .bottom-stripe {
            height: 4px;
            background: linear-gradient(to right, #8B6914, #D4AF37, #F0D060, #D4AF37, #8B6914);
        }
    </style>
</head>
<body>
<div class="poster">

    <div class="top-stripe"></div>

    {{-- HEADER --}}
    <div class="header">
        <table class="header-inner">
            <tr>
                <td>
                    <div class="brand-name">Anggita Wedding Organizer</div>
                    <div class="brand-sub">Surabaya &nbsp;·&nbsp; Professional Wedding Services</div>
                </td>
                <td>
                    <div class="header-contact">
                        anggitaweddingsurabaya@gmail.com &nbsp;|&nbsp; @anggitawedding &nbsp;|&nbsp; anggitaweddingsby.com
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- HERO --}}
    <div class="hero-band">
        <div><span class="category-pill">{{ $package->category_label }}</span></div>
        <div class="package-name">{{ $package->name }}</div>
        @if($package->tier)
            @php $tierClass = match($package->tier) { 'silver' => 'tier-silver', 'gold' => 'tier-gold', default => 'tier-premium' }; @endphp
            <span class="tier-badge {{ $tierClass }}">&#10022; {{ ucfirst($package->tier) }} &#10022;</span>
        @endif
    </div>

    {{-- CONTENT AREA --}}
    <div class="content">

        {{-- PRICE + DESC --}}
        <table class="meta-table">
            <tr>
                <td style="width:38%;vertical-align:top;">
                    <div class="price-block">
                        @if($package->hasActivePromo())
                            <div class="promo-tag">&#9889; {{ $package->promo_label ?? 'Promo Spesial' }}</div>
                            <div class="price-label">Harga Normal</div>
                            <div class="price-original">{{ $package->formatted_price }}</div>
                            <div class="price-label" style="margin-top:4px;">Harga Promo</div>
                            <div class="price-main">{{ $package->formattedEffectivePrice }}</div>
                        @else
                            <div class="price-label">Mulai Dari</div>
                            <div class="price-main">{{ $package->formatted_price }}</div>
                        @endif
                        <div class="price-dp">DP 30%: <strong>Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</strong></div>
                    </div>
                </td>
                <td style="vertical-align:top;">
                    <div class="side-meta">
                        @if($package->description)
                            <div class="desc-text">{{ $package->description }}</div>
                        @endif
                        @if($package->has_digital_invitation)
                            <div class="invite-badge">&#9993;&nbsp; Termasuk Undangan Digital Premium</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- ORNAMENT DIVIDER --}}
        <div class="ornament">&#9670; &nbsp; &nbsp; &#9670;</div>

        {{-- FEATURES --}}
        @php $sections = $package->feature_sections; @endphp
        @if(!empty($sections))
        <div class="section-eyebrow">Apa Yang Anda Dapatkan</div>

        @if(count($sections) === 1 && !$sections[0]['title'])
            {{-- Flat items in 3 columns --}}
            @php $chunks = array_chunk($sections[0]['items'], 3); @endphp
            <table class="flat-table">
                @foreach($chunks as $row)
                <tr>
                    @foreach($row as $item)
                        <td class="flat-cell"><span class="flat-check">&#10003;</span>{{ $item }}</td>
                    @endforeach
                    @for($i = count($row); $i < 3; $i++)
                        <td style=""></td>
                    @endfor
                </tr>
                @endforeach
            </table>
        @else
            {{-- Sectioned grid --}}
            @php $sectionRows = array_chunk($sections, $cols); @endphp
            <table class="features-table">
                @foreach($sectionRows as $rowItems)
                <tr>
                    @foreach($rowItems as $section)
                    <td class="feature-cell">
                        @if($section['title'])
                            <div class="feature-cell-title">{{ $section['title'] }}</div>
                        @endif
                        @foreach($section['items'] as $item)
                            <div class="feature-item">{{ $item }}</div>
                        @endforeach
                    </td>
                    @endforeach
                    @for($i = count($rowItems); $i < $cols; $i++)
                        <td style=""></td>
                    @endfor
                </tr>
                @endforeach
            </table>
        @endif
        @endif

    </div>{{-- /content --}}

    {{-- FOOTER --}}
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <div class="footer-cta">Hubungi Kami Sekarang</div>
                    <div class="footer-sub">Konsultasi gratis &nbsp;&#183;&nbsp; DP ringan 30% &nbsp;&#183;&nbsp; Garansi kepuasan penuh</div>
                </td>
                <td>
                    <div class="footer-contacts">
                        <strong>WA:</strong> +62 812-3456-7890 &nbsp;&nbsp;
                        <strong>IG:</strong> @anggitawedding &nbsp;&nbsp;
                        <strong>Web:</strong> anggitaweddingsby.com
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="bottom-stripe"></div>
</div>
</body>
</html>
