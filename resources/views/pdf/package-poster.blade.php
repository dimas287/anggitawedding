<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #0D0501;
            color: #fff;
            width: 210mm;
            min-height: 297mm;
        }

        /* ─── OUTER WRAPPER ─── */
        .poster {
            width: 210mm;
            min-height: 297mm;
            background: #0D0501;
            position: relative;
        }

        /* ─── TOP DECORATIVE BAR ─── */
        .top-bar {
            background: #D4AF37;
            height: 6px;
            width: 100%;
        }

        /* ─── HEADER ─── */
        .header {
            padding: 28px 32px 20px;
            border-bottom: 1px solid rgba(212,175,55,0.25);
        }
        .brand-row {
            width: 100%;
        }
        .brand-row td {
            vertical-align: middle;
        }
        .brand-name {
            font-size: 11px;
            font-weight: bold;
            color: #D4AF37;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 8px;
            color: rgba(212,175,55,0.6);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .contact-info {
            font-size: 8px;
            color: rgba(255,255,255,0.5);
            text-align: right;
        }
        .contact-info div {
            margin-bottom: 2px;
        }

        /* ─── HERO SECTION ─── */
        .hero {
            padding: 32px 32px 24px;
            text-align: center;
            position: relative;
        }
        .eyebrow {
            font-size: 8.5px;
            font-weight: bold;
            color: #D4AF37;
            letter-spacing: 5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .hero-ornament {
            color: rgba(212,175,55,0.3);
            font-size: 14px;
            letter-spacing: 6px;
            margin-bottom: 10px;
        }
        .package-name {
            font-size: 34px;
            font-weight: bold;
            color: #fff;
            letter-spacing: 1px;
            line-height: 1.15;
            margin-bottom: 10px;
        }

        /* tier badge */
        .tier-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .tier-silver { background: rgba(156,163,175,0.2); color: #9CA3AF; border: 1px solid rgba(156,163,175,0.4); }
        .tier-gold   { background: rgba(212,175,55,0.15); color: #D4AF37; border: 1px solid rgba(212,175,55,0.5); }
        .tier-premium, .tier-platinum { background: rgba(139,92,246,0.15); color: #A78BFA; border: 1px solid rgba(139,92,246,0.4); }

        /* ─── PRICE BLOCK ─── */
        .price-block {
            background: rgba(212,175,55,0.08);
            border: 1px solid rgba(212,175,55,0.3);
            border-radius: 12px;
            padding: 16px 24px;
            margin: 0 32px 24px;
            text-align: center;
        }
        .price-label {
            font-size: 8.5px;
            color: rgba(212,175,55,0.7);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .price-original {
            font-size: 11px;
            color: rgba(255,255,255,0.35);
            text-decoration: line-through;
            margin-bottom: 2px;
        }
        .price-main {
            font-size: 38px;
            font-weight: bold;
            color: #D4AF37;
            line-height: 1;
            margin-bottom: 6px;
        }
        .price-dp {
            font-size: 9px;
            color: rgba(255,255,255,0.5);
        }
        .price-dp span {
            color: rgba(212,175,55,0.8);
            font-weight: bold;
        }
        .promo-tag {
            display: inline-block;
            background: rgba(236,72,153,0.2);
            border: 1px solid rgba(236,72,153,0.4);
            color: #F9A8D4;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 3px 12px;
            border-radius: 20px;
            margin-bottom: 8px;
        }

        /* ─── DIVIDER ─── */
        .gold-divider {
            text-align: center;
            color: rgba(212,175,55,0.4);
            font-size: 11px;
            letter-spacing: 8px;
            margin: 0 32px 20px;
        }

        /* ─── DESCRIPTION ─── */
        .description {
            margin: 0 32px 20px;
            font-size: 10px;
            color: rgba(255,255,255,0.65);
            text-align: center;
            line-height: 1.6;
        }

        /* ─── FEATURES GRID ─── */
        .features-wrapper {
            margin: 0 32px 24px;
        }
        .section-eyebrow {
            font-size: 8px;
            color: rgba(212,175,55,0.6);
            letter-spacing: 3px;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 14px;
        }
        .features-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .feature-cell {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(212,175,55,0.12);
            border-radius: 8px;
            padding: 10px 12px;
            vertical-align: top;
            width: 50%;
        }
        .feature-cell-title {
            font-size: 8px;
            font-weight: bold;
            color: #D4AF37;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 7px;
            padding-bottom: 5px;
            border-bottom: 1px solid rgba(212,175,55,0.2);
        }
        .feature-item {
            font-size: 9px;
            color: rgba(255,255,255,0.8);
            padding: 2.5px 0;
            padding-left: 12px;
            position: relative;
            line-height: 1.4;
        }
        .feature-item::before {
            content: "✓";
            color: #D4AF37;
            position: absolute;
            left: 0;
            font-size: 8px;
        }

        /* ─── FLAT FEATURES (no section) ─── */
        .flat-features-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
        }
        .flat-feature-cell {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(212,175,55,0.1);
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 9px;
            color: rgba(255,255,255,0.8);
            width: 33.33%;
            vertical-align: middle;
        }
        .check-icon {
            color: #D4AF37;
            margin-right: 4px;
        }

        /* ─── DIGITAL INVITATION BADGE ─── */
        .invite-badge {
            margin: 0 32px 20px;
            background: rgba(212,175,55,0.08);
            border: 1px solid rgba(212,175,55,0.25);
            border-radius: 8px;
            padding: 10px 16px;
            text-align: center;
        }
        .invite-badge-text {
            font-size: 9px;
            color: rgba(212,175,55,0.9);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* ─── CATEGORY TAG ─── */
        .category-tag {
            margin: 0 32px 16px;
            text-align: center;
        }
        .category-pill {
            display: inline-block;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 8.5px;
            color: rgba(255,255,255,0.5);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* ─── FOOTER ─── */
        .footer {
            background: rgba(212,175,55,0.06);
            border-top: 1px solid rgba(212,175,55,0.2);
            padding: 16px 32px;
            margin-top: 8px;
        }
        .footer-table {
            width: 100%;
        }
        .footer-table td {
            vertical-align: middle;
        }
        .footer-cta {
            font-size: 10px;
            color: rgba(212,175,55,0.9);
            font-weight: bold;
            letter-spacing: 1px;
        }
        .footer-sub {
            font-size: 8px;
            color: rgba(255,255,255,0.4);
            margin-top: 2px;
        }
        .footer-contacts {
            font-size: 8px;
            color: rgba(255,255,255,0.4);
            text-align: right;
            line-height: 1.7;
        }
        .footer-contacts strong {
            color: rgba(212,175,55,0.7);
        }

        /* ─── BOTTOM BAR ─── */
        .bottom-bar {
            background: #D4AF37;
            height: 4px;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="poster">
    <div class="top-bar"></div>

    {{-- HEADER --}}
    <div class="header">
        <table class="brand-row">
            <tr>
                <td>
                    <div class="brand-name">Anggita Wedding Organizer</div>
                    <div class="brand-sub">Surabaya · Professional Wedding Services</div>
                </td>
                <td>
                    <div class="contact-info">
                        <div>anggitaweddingsurabaya@gmail.com</div>
                        <div>@anggitawedding · anggitaweddingsby.com</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- HERO --}}
    <div class="hero">
        <div class="eyebrow">— Paket Spesial —</div>
        <div class="category-tag" style="margin:0 0 12px;">
            <span class="category-pill">{{ $package->category_label }}</span>
        </div>
        <div class="package-name">{{ $package->name }}</div>
        @if($package->tier)
            @php
                $tierClass = match($package->tier) {
                    'silver' => 'tier-silver',
                    'gold' => 'tier-gold',
                    default => 'tier-premium',
                };
            @endphp
            <div><span class="tier-badge {{ $tierClass }}">✦ {{ ucfirst($package->tier) }} ✦</span></div>
        @endif
    </div>

    {{-- PRICE BLOCK --}}
    <div class="price-block">
        @if($package->hasActivePromo())
            <div class="promo-tag">⚡ {{ $package->promo_label ?? 'Promo Spesial' }}</div>
            <div class="price-label">Harga Normal</div>
            <div class="price-original">{{ $package->formatted_price }}</div>
            <div class="price-label" style="margin-top:8px;">Harga Promo</div>
            <div class="price-main">{{ $package->formattedEffectivePrice }}</div>
        @else
            <div class="price-label">Mulai Dari</div>
            <div class="price-main">{{ $package->formatted_price }}</div>
        @endif
        <div class="price-dp">
            Cicilan DP 30%: <span>Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- DESCRIPTION --}}
    @if($package->description)
    <div class="description">{{ $package->description }}</div>
    @endif

    {{-- GOLD DIVIDER --}}
    <div class="gold-divider">· · · · ◆ · · · ·</div>

    {{-- DIGITAL INVITATION BADGE --}}
    @if($package->has_digital_invitation)
    <div class="invite-badge">
        <div class="invite-badge-text">✉ Termasuk Undangan Digital Premium</div>
    </div>
    @endif

    {{-- FEATURES --}}
    @php $sections = $package->feature_sections; @endphp
    @if(!empty($sections))
    <div class="features-wrapper">
        <div class="section-eyebrow">— Yang Anda Dapatkan —</div>

        @if(count($sections) === 1 && !$sections[0]['title'])
            {{-- Flat list in 3-column grid --}}
            @php
                $allItems = $sections[0]['items'];
                $chunks = array_chunk($allItems, 3);
            @endphp
            <table class="flat-features-table">
                @foreach($chunks as $row)
                <tr>
                    @foreach($row as $item)
                    <td class="flat-feature-cell">
                        <span class="check-icon">✓</span>{{ $item }}
                    </td>
                    @endforeach
                    @for($i = count($row); $i < 3; $i++)
                    <td class="flat-feature-cell" style="border:none;background:none;"></td>
                    @endfor
                </tr>
                @endforeach
            </table>
        @else
            {{-- Sectioned 2-column grid --}}
            @php $sectionPairs = array_chunk($sections, 2); @endphp
            <table class="features-table">
                @foreach($sectionPairs as $pair)
                <tr>
                    @foreach($pair as $section)
                    <td class="feature-cell">
                        @if($section['title'])
                        <div class="feature-cell-title">{{ $section['title'] }}</div>
                        @endif
                        @foreach(array_slice($section['items'], 0, 8) as $item)
                        <div class="feature-item">{{ $item }}</div>
                        @endforeach
                        @if(count($section['items']) > 8)
                        <div class="feature-item" style="color:rgba(212,175,55,0.5);font-style:italic;">
                            + {{ count($section['items']) - 8 }} item lainnya...
                        </div>
                        @endif
                    </td>
                    @endforeach
                    @if(count($pair) === 1)
                    <td class="feature-cell" style="border:none;background:none;"></td>
                    @endif
                </tr>
                @endforeach
            </table>
        @endif
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <div class="footer-cta">Hubungi Kami Sekarang</div>
                    <div class="footer-sub">Konsultasi gratis · DP ringan 30% · Garansi kepuasan</div>
                </td>
                <td>
                    <div class="footer-contacts">
                        <strong>WA:</strong> +62 812-3456-7890<br>
                        <strong>IG:</strong> @anggitawedding<br>
                        <strong>Web:</strong> anggitaweddingsby.com
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="bottom-bar"></div>
</div>
</body>
</html>
