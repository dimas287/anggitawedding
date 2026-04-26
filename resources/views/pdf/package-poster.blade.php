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
            height: 297mm;
            overflow: hidden;
        }

        /* ─── LAYOUT WRAPPER ─── */
        .poster {
            width: 210mm;
            height: 297mm;
            background: #0D0501;
            display: block;
        }

        /* ─── TOP BAR ─── */
        .top-bar { background: #D4AF37; height: 5px; width: 100%; }

        /* ─── HEADER ─── */
        .header {
            padding: 10px 24px 8px;
            border-bottom: 1px solid rgba(212,175,55,0.2);
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .brand-name { font-size: 12px; font-weight: bold; color: #D4AF37; letter-spacing: 2px; }
        .brand-sub { font-size: 7px; color: rgba(212,175,55,0.55); letter-spacing: 2px; text-transform: uppercase; }
        .contact-info { font-size: 7px; color: rgba(255,255,255,0.4); text-align: right; line-height: 1.6; }

        /* ─── HERO ─── */
        .hero { padding: 10px 24px 6px; text-align: center; }
        .category-pill {
            display: inline-block;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 7px;
            color: rgba(255,255,255,0.4);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .package-name {
            font-size: 26px;
            font-weight: bold;
            color: #fff;
            letter-spacing: 0.5px;
            line-height: 1.1;
            margin-bottom: 5px;
        }
        .tier-badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 2.5px;
            text-transform: uppercase;
        }
        .tier-silver { background: rgba(156,163,175,0.15); color: #9CA3AF; border: 1px solid rgba(156,163,175,0.35); }
        .tier-gold   { background: rgba(212,175,55,0.12); color: #D4AF37; border: 1px solid rgba(212,175,55,0.45); }
        .tier-premium, .tier-platinum { background: rgba(139,92,246,0.12); color: #A78BFA; border: 1px solid rgba(139,92,246,0.35); }

        /* ─── PRICE + DESCRIPTION ROW ─── */
        .meta-row { padding: 6px 24px 6px; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { vertical-align: middle; }
        .price-block {
            background: rgba(212,175,55,0.07);
            border: 1px solid rgba(212,175,55,0.28);
            border-radius: 8px;
            padding: 8px 14px;
            width: 46%;
        }
        .price-label { font-size: 7px; color: rgba(212,175,55,0.6); letter-spacing: 2.5px; text-transform: uppercase; margin-bottom: 2px; }
        .price-main { font-size: 24px; font-weight: bold; color: #D4AF37; line-height: 1; margin-bottom: 3px; }
        .price-original { font-size: 9px; color: rgba(255,255,255,0.3); text-decoration: line-through; margin-bottom: 1px; }
        .price-dp { font-size: 7.5px; color: rgba(255,255,255,0.45); }
        .price-dp span { color: rgba(212,175,55,0.8); font-weight: bold; }
        .promo-tag {
            display: inline-block;
            background: rgba(236,72,153,0.18);
            border: 1px solid rgba(236,72,153,0.35);
            color: #F9A8D4;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 20px;
            margin-bottom: 4px;
        }
        .desc-block { padding-left: 16px; }
        .desc-text { font-size: 8px; color: rgba(255,255,255,0.55); line-height: 1.55; margin-bottom: 5px; }
        .invite-badge {
            background: rgba(212,175,55,0.07);
            border: 1px solid rgba(212,175,55,0.22);
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 7px;
            color: rgba(212,175,55,0.85);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ─── DIVIDER ─── */
        .divider { text-align: center; color: rgba(212,175,55,0.3); font-size: 10px; letter-spacing: 6px; padding: 4px 0; }

        /* ─── FEATURES ─── */
        .features-wrapper { padding: 0 24px 6px; }
        .section-eyebrow {
            font-size: 7px;
            color: rgba(212,175,55,0.55);
            letter-spacing: 3px;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 7px;
        }

        /* Sectioned 3-column grid */
        .features-table { width: 100%; border-collapse: separate; border-spacing: 4px; }
        .feature-cell {
            background: rgba(255,255,255,0.035);
            border: 1px solid rgba(212,175,55,0.1);
            border-radius: 6px;
            padding: 7px 9px;
            vertical-align: top;
        }
        .feature-cell-title {
            font-size: 7px;
            font-weight: bold;
            color: #D4AF37;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px solid rgba(212,175,55,0.18);
        }
        .feature-item {
            font-size: 7.5px;
            color: rgba(255,255,255,0.78);
            padding: 1.8px 0;
            padding-left: 10px;
            position: relative;
            line-height: 1.35;
        }
        .feature-item::before {
            content: "✓";
            color: #D4AF37;
            position: absolute;
            left: 0;
            font-size: 7px;
        }

        /* Flat 3-column grid */
        .flat-table { width: 100%; border-collapse: separate; border-spacing: 3px; }
        .flat-cell {
            background: rgba(255,255,255,0.035);
            border: 1px solid rgba(212,175,55,0.1);
            border-radius: 5px;
            padding: 5px 8px;
            font-size: 8px;
            color: rgba(255,255,255,0.78);
            vertical-align: middle;
        }
        .check { color: #D4AF37; margin-right: 3px; font-size: 7px; }

        /* ─── FOOTER ─── */
        .footer {
            background: rgba(212,175,55,0.05);
            border-top: 1px solid rgba(212,175,55,0.18);
            padding: 8px 24px;
        }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { vertical-align: middle; }
        .footer-cta { font-size: 9px; color: rgba(212,175,55,0.9); font-weight: bold; letter-spacing: 0.5px; }
        .footer-sub { font-size: 7px; color: rgba(255,255,255,0.35); margin-top: 2px; }
        .footer-contacts { font-size: 7.5px; color: rgba(255,255,255,0.4); text-align: right; line-height: 1.7; }
        .footer-contacts strong { color: rgba(212,175,55,0.7); }

        /* ─── BOTTOM BAR ─── */
        .bottom-bar { background: #D4AF37; height: 4px; width: 100%; position: absolute; bottom: 0; left: 0; }
    </style>
</head>
<body>
<div class="poster">
    <div class="top-bar"></div>

    {{-- HEADER --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="brand-name">Anggita Wedding Organizer</div>
                    <div class="brand-sub">Surabaya · Professional Wedding Services</div>
                </td>
                <td>
                    <div class="contact-info">
                        anggitaweddingsurabaya@gmail.com &nbsp;·&nbsp; @anggitawedding &nbsp;·&nbsp; anggitaweddingsby.com
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- HERO --}}
    <div class="hero">
        <div><span class="category-pill">{{ $package->category_label }}</span></div>
        <div class="package-name">{{ $package->name }}</div>
        @if($package->tier)
            @php
                $tierClass = match($package->tier) { 'silver' => 'tier-silver', 'gold' => 'tier-gold', default => 'tier-premium' };
            @endphp
            <div><span class="tier-badge {{ $tierClass }}">✦ {{ ucfirst($package->tier) }} ✦</span></div>
        @endif
    </div>

    {{-- PRICE + DESC ROW --}}
    <div class="meta-row">
        <table class="meta-table">
            <tr>
                <td style="width:46%;vertical-align:top;">
                    <div class="price-block">
                        @if($package->hasActivePromo())
                            <div class="promo-tag">⚡ {{ $package->promo_label ?? 'Promo Spesial' }}</div>
                            <div class="price-label">Harga Normal</div>
                            <div class="price-original">{{ $package->formatted_price }}</div>
                            <div class="price-label" style="margin-top:4px;">Harga Promo</div>
                            <div class="price-main">{{ $package->formattedEffectivePrice }}</div>
                        @else
                            <div class="price-label">Mulai Dari</div>
                            <div class="price-main">{{ $package->formatted_price }}</div>
                        @endif
                        <div class="price-dp">DP 30%: <span>Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</span></div>
                    </div>
                </td>
                <td style="vertical-align:top;padding-left:14px;">
                    @if($package->description)
                        <div class="desc-text">{{ $package->description }}</div>
                    @endif
                    @if($package->has_digital_invitation)
                        <div class="invite-badge">✉&nbsp; Termasuk Undangan Digital Premium</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- DIVIDER --}}
    <div class="divider">· · ◆ · ·</div>

    {{-- FEATURES --}}
    @php $sections = $package->feature_sections; @endphp
    @if(!empty($sections))
    <div class="features-wrapper">
        <div class="section-eyebrow">— Yang Anda Dapatkan —</div>

        @if(count($sections) === 1 && !$sections[0]['title'])
            {{-- Flat 3-column grid — ALL items --}}
            @php $chunks = array_chunk($sections[0]['items'], 3); @endphp
            <table class="flat-table">
                @foreach($chunks as $row)
                <tr>
                    @foreach($row as $item)
                    <td class="flat-cell"><span class="check">✓</span>{{ $item }}</td>
                    @endforeach
                    @for($i = count($row); $i < 3; $i++)
                    <td class="flat-cell" style="border:none;background:none;"></td>
                    @endfor
                </tr>
                @endforeach
            </table>
        @else
            {{-- Sectioned grid — 3 columns per row, ALL items --}}
            @php $sectionChunks = array_chunk($sections, 3); @endphp
            <table class="features-table">
                @foreach($sectionChunks as $rowSections)
                <tr>
                    @foreach($rowSections as $section)
                    <td class="feature-cell">
                        @if($section['title'])
                            <div class="feature-cell-title">{{ $section['title'] }}</div>
                        @endif
                        @foreach($section['items'] as $item)
                            <div class="feature-item">{{ $item }}</div>
                        @endforeach
                    </td>
                    @endforeach
                    @for($i = count($rowSections); $i < 3; $i++)
                    <td class="feature-cell" style="border:none;background:none;"></td>
                    @endfor
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
                    <div class="footer-sub">Konsultasi gratis &nbsp;·&nbsp; DP ringan 30% &nbsp;·&nbsp; Garansi kepuasan</div>
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

    <div class="bottom-bar"></div>
</div>
</body>
</html>
