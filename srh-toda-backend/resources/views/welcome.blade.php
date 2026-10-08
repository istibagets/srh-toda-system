<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ \App\Support\SystemSettings::brandName() }} — Santa Rosa Homes TODA</title>
    
    <!-- PWA & Mobile Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ \App\Support\SystemSettings::brandName() }}">
    <meta name="theme-color" content="#0f172a">
    
    <!-- Favicon & Icons -->
    <link rel="icon" type="image/png" href="{{ srh_logo_url() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ srh_logo_url() }}">
    
    <!-- Local High-Performance Fonts (Zero External Network Delay) -->
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --navy-900: #0b132b;
            --navy-800: #0f172a;
            --navy-700: #1e293b;
            --navy-600: #334155;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --border-hover: #cbd5e1;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            --shadow-subtle: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
            --shadow-card: 0 4px 20px -2px rgba(15,23,42,0.06), 0 2px 6px -1px rgba(15,23,42,0.04);
            --shadow-hover: 0 14px 28px -4px rgba(15,23,42,0.09), 0 4px 10px -2px rgba(15,23,42,0.04);
            --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px;
            -webkit-text-size-adjust: 100%;
        }
        section {
            scroll-margin-top: 90px;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-dark);
            line-height: 1.6;
            letter-spacing: normal;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            overflow-x: hidden;
            width: 100%;
        }

        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; }
        
        /* ── Container ── */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            box-sizing: border-box;
        }
        
        /* ── Broadcast / Advisory Banner ── */
        .advisory-bar {
            background: #0f172a;
            color: #f1f5f9;
            font-size: 0.82rem;
            padding: 0.55rem 1rem;
            border-bottom: 1px solid #1e293b;
        }
        .advisory-bar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .advisory-content {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
            flex: 1;
        }
        .advisory-badge {
            background: #2563eb;
            color: #fff;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.15rem 0.55rem;
            border-radius: 4px;
            flex-shrink: 0;
        }
        .advisory-text {
            font-weight: 500;
            color: #cbd5e1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }
        .advisory-text strong {
            color: #ffffff;
            font-weight: 700;
            margin-right: 0.25rem;
        }
        .advisory-link {
            color: #93c5fd;
            font-weight: 600;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: color 0.15s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .advisory-link:hover { color: #ffffff; text-decoration: underline; }

        /* ── Top Navigation Header ── */
        .header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), background 0.2s ease, box-shadow 0.2s ease;
            will-change: transform;
        }
        .header.header-hidden {
            transform: translateY(-100%) !important;
        }
        .header.header-scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            background: rgba(255, 255, 255, 0.98);
        }
        .header-inner {
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
            cursor: pointer;
            text-decoration: none;
        }
        .brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: transparent;
            padding: 0;
            object-fit: contain;
            transition: transform 0.2s var(--ease-out);
        }
        .brand:hover .brand-logo {
            transform: scale(1.04);
        }
        .brand-text {
            display: flex;
            flex-direction: column;
        }
        .brand-title {
            font-size: 1.08rem;
            font-weight: 800;
            letter-spacing: normal;
            color: #0f172a;
            line-height: 1.1;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.75rem;
            list-style: none;
        }
        .nav-link-item {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--navy-600);
            transition: color 0.15s ease;
            position: relative;
            padding: 0.35rem 0;
            white-space: nowrap;
            text-decoration: none;
        }
        .nav-link-item:hover {
            color: var(--primary);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            font-size: 0.86rem;
            font-weight: 700;
            padding: 0.6rem 1.25rem;
            border-radius: var(--radius-sm);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s var(--ease-out);
            text-decoration: none;
            white-space: nowrap;
            user-select: none;
        }
        .btn:active {
            transform: scale(0.97);
        }
        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            box-shadow: 0 6px 16px rgba(37,99,235,0.28);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #ffffff;
            color: #0f172a;
            border-color: var(--border-color);
        }
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: var(--border-hover);
            transform: translateY(-1px);
        }
        .btn-outline {
            background: transparent;
            color: #0f172a;
            border-color: var(--border-color);
        }
        .btn-outline:hover {
            background: #f1f5f9;
            border-color: var(--border-hover);
        }
        .btn-lg {
            padding: 0.75rem 1.6rem;
            font-size: 0.95rem;
            border-radius: var(--radius-md);
        }
        .btn-hero {
            height: 52px;
            font-size: 0.98rem;
            font-weight: 700;
            border-radius: 14px;
            padding: 0 1.75rem;
            letter-spacing: -0.01em;
        }
        .btn-primary.btn-hero {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 6px 20px -2px rgba(37, 99, 235, 0.4);
            border: 1px solid #2563eb;
            color: #ffffff;
        }
        .btn-primary.btn-hero:hover {
            box-shadow: 0 8px 26px -2px rgba(37, 99, 235, 0.5);
            transform: translateY(-2px);
        }
        .btn-secondary.btn-hero {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            color: #0f172a;
        }
        .btn-secondary.btn-hero:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-2px);
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: 1px solid var(--border-color);
            padding: 0.45rem;
            border-radius: 8px;
            color: #0f172a;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .mobile-menu-toggle:hover {
            background: #f1f5f9;
        }

        /* ── Modern Keyframes & Fluid Entrance Animations ── */
        @keyframes heroFadeInUp {
            from {
                opacity: 0;
                transform: translateY(22px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes subtlePulseGlow {
            0% { box-shadow: 0 6px 20px -2px rgba(37, 99, 235, 0.42); }
            50% { box-shadow: 0 10px 28px 0 rgba(37, 99, 235, 0.58); }
            100% { box-shadow: 0 6px 20px -2px rgba(37, 99, 235, 0.42); }
        }

        /* ── Scroll-Triggered Reveal Animations ── */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.85s cubic-bezier(0.16, 1, 0.3, 1), transform 0.85s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .stagger-1 { transition-delay: 0.06s; }
        .stagger-2 { transition-delay: 0.12s; }
        .stagger-3 { transition-delay: 0.18s; }
        .stagger-4 { transition-delay: 0.24s; }

        /* ── Reimagined Hero Showcase Frame ── */
        .hero {
            position: relative;
            padding: 4.25rem 0 3.75rem;
            background: radial-gradient(circle at top center, rgba(37,99,235,0.06) 0%, rgba(248,250,252,0) 70%), #f8fafc;
            border-bottom: 1px solid var(--border-color);
            overflow: hidden;
        }
        .hero-inner {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 2.75rem;
            align-items: center;
        }
        .hero-card-frame {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 2.75rem 2.25rem 2.25rem;
            box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.06), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
            box-sizing: border-box;
            width: 100%;
            animation: heroFadeInUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            will-change: transform, opacity;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hero-title {
            font-size: 2.35rem;
            font-weight: 800;
            line-height: 1.22;
            letter-spacing: -0.025em;
            color: #0f172a;
            margin-bottom: 1.1rem;
            word-break: break-word;
        }
        .hero-title-accent {
            color: var(--primary);
        }
        .hero-subtitle {
            font-size: 1rem;
            line-height: 1.65;
            color: #475569;
            margin-bottom: 2rem;
        }
        .hero-ctas {
            margin-bottom: 2rem;
            width: 100%;
        }
        .btn-hero-single {
            width: 100%;
            height: 52px;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 6px 20px -2px rgba(37, 99, 235, 0.42);
            border: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-hero-single:hover {
            box-shadow: 0 10px 28px -2px rgba(37, 99, 235, 0.55);
            transform: translateY(-2px);
        }
        .btn-hero-single:active {
            transform: scale(0.98);
        }
        .hero-card-trust {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding-top: 1.35rem;
            border-top: 1px solid var(--border-color);
            flex-wrap: wrap;
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            font-size: 0.84rem;
            font-weight: 650;
            color: var(--navy-700);
            white-space: nowrap;
        }
        .trust-icon-pill {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .trust-item:hover .trust-icon-pill {
            transform: scale(1.15);
        }
        .trust-icon {
            color: #10b981;
            flex-shrink: 0;
        }

        /* ── Hero Terminal Card (Right Panel) ── */
        .terminal-preview-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1.85rem;
            box-shadow: var(--shadow-card);
            animation: heroFadeInUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.12s forwards;
            opacity: 0;
            animation-fill-mode: forwards;
            will-change: transform, opacity;
            transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.32s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .terminal-preview-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }
        .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.25rem;
            gap: 0.5rem;
        }
        .preview-title {
            font-size: 1.02rem;
            font-weight: 800;
            color: #0f172a;
        }
        .preview-metric-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .preview-metric {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 0.85rem;
            transition: border-color 0.2s ease, transform 0.2s ease;
        }
        .preview-metric:hover {
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        .metric-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }
        .metric-val {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
        }
        .preview-station-info {
            font-size: 0.84rem;
            color: var(--text-muted);
            line-height: 1.5;
            background: #f8fafc;
            border-radius: var(--radius-sm);
            padding: 0.85rem;
            border-left: 3px solid var(--primary);
        }

        /* ── Sections & Components ── */
        .section {
            padding: 5rem 0;
            position: relative;
        }
        .section-alt {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }
        .section-header {
            text-align: center;
            max-width: 680px;
            margin: 0 auto 3.25rem auto;
        }
        .section-tag {
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        .section-title {
            font-size: 2.15rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
            line-height: 1.28;
            margin-bottom: 0.85rem;
        }
        .section-desc {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.65;
            max-width: 620px;
            margin: 0 auto;
        }

        .cards-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }
        .cards-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.75rem;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.75rem;
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease, box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            border-color: #cbd5e1;
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.08);
        }
        .feature-icon-wrap {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            transition: transform 0.2s var(--ease-out);
        }
        .feature-card:hover .feature-icon-wrap {
            transform: scale(1.08);
        }
        .feature-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        .feature-text {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* ── Membership Steps ── */
        .step-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.75rem;
            position: relative;
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
        }
        .step-card:hover {
            transform: translateY(-5px);
            border-color: #cbd5e1;
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.08);
        }
        .step-number {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 800;
            color: var(--primary);
            background: var(--primary-light);
            padding: 0.25rem 0.65rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            letter-spacing: 0.04em;
        }
        .step-title {
            font-size: 1.02rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        .step-text {
            font-size: 0.86rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* ── Announcements ── */
        .announcement-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
        }
        .announcement-card:hover {
            transform: translateY(-5px);
            border-color: #cbd5e1;
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.08);
        }
        .announcement-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.75rem;
            color: var(--text-light);
            margin-bottom: 0.85rem;
        }
        .announcement-badge {
            background: #f1f5f9;
            color: var(--navy-700);
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .announcement-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        .announcement-body {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* ── Announcements Empty State Card ── */
        .announcement-empty-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.25rem;
            box-shadow: var(--shadow-card);
            max-width: 680px;
            margin: 0 auto;
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .announcement-empty-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }
        .empty-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .empty-text-wrap {
            max-width: 520px;
            text-align: center;
        }
        .empty-title {
            font-size: 1.12rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }
        .empty-desc {
            font-size: 0.92rem;
            color: var(--text-muted);
            line-height: 1.6;
        }
        .empty-badge-status {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 0.35rem 0.85rem;
            border-radius: var(--radius-full);
            font-size: 0.76rem;
            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ── FAQ Accordion ── */
        .faq-wrapper {
            max-width: 800px;
            margin: 0 auto;
        }
        .faq-item {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            margin-bottom: 0.85rem;
            overflow: hidden;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .faq-item.active {
            border-color: var(--primary);
            box-shadow: 0 4px 16px rgba(37,99,235,0.08);
        }
        .faq-btn {
            width: 100%;
            text-align: left;
            padding: 1.15rem 1.4rem;
            background: none;
            border: none;
            font-size: 0.96rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            cursor: pointer;
            user-select: none;
        }
        .faq-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            color: var(--text-muted);
            transition: transform 0.3s var(--ease-out), color 0.2s ease;
        }
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
            color: var(--primary);
        }
        .faq-body {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            padding: 0 1.4rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
            transition: max-height 0.35s var(--ease-out), opacity 0.3s ease, padding 0.3s ease;
        }
        .faq-item.active .faq-body {
            max-height: 400px;
            opacity: 1;
            padding: 0 1.4rem 1.25rem;
        }

        /* ── Footer Contact List ── */
        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            font-size: 0.86rem;
            color: #cbd5e1;
            line-height: 1.45;
        }
        .footer-contact-icon {
            width: 17px;
            height: 17px;
            color: #60a5fa;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .footer-contact-label {
            color: #94a3b8;
            font-weight: 500;
            margin-right: 0.25rem;
        }
        .footer-contact-val {
            color: #e2e8f0;
            font-weight: 600;
            word-break: break-word;
        }

        /* ── Footer ── */
        .footer {
            background: #0b132b;
            color: #94a3b8;
            padding: 4rem 0 2rem;
            border-top: 1px solid #1e293b;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
            gap: 3rem;
            margin-bottom: 3.5rem;
        }
        .footer-brand-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }
        .footer-brand-desc {
            font-size: 0.88rem;
            line-height: 1.6;
            margin-bottom: 1.25rem;
            color: #94a3b8;
        }
        .footer-col-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #ffffff;
            margin-bottom: 1.25rem;
        }
        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }
        .footer-links a {
            font-size: 0.88rem;
            color: #94a3b8;
            transition: color 0.15s ease;
            text-decoration: none;
        }
        .footer-links a:hover {
            color: #ffffff;
        }
        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 2rem;
            border-top: 1px solid #1e293b;
            font-size: 0.8rem;
            color: #64748b;
            flex-wrap: wrap;
            gap: 1rem;
        }

        /* ── Mobile Navigation Drawer ── */
        .mobile-nav-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 300px;
            max-width: 85vw;
            background: #ffffff;
            z-index: 100;
            box-shadow: -4px 0 24px rgba(0,0,0,0.15);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transform: translateX(100%);
            transition: transform 0.3s var(--ease-out);
        }
        .mobile-nav-drawer.open {
            transform: translateX(0);
        }
        .mobile-drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.5);
            backdrop-filter: blur(4px);
            z-index: 99;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .mobile-drawer-backdrop.open {
            opacity: 1;
            pointer-events: auto;
        }
        .drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }
        .drawer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .drawer-link-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--navy-700);
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .drawer-link-item:hover {
            background: #f1f5f9;
            color: var(--primary);
        }
        .drawer-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        /* ── Responsive Overrides ── */
        @media (max-width: 1024px) {
            .hero-inner {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }
            .cards-grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
            .cards-grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
            .contact-box {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 2.25rem;
            }
        }

        @media (max-width: 768px) {
            html, body {
                width: 100%;
                max-width: 100vw;
                overflow-x: hidden !important;
                text-align: left;
            }
            .container {
                padding: 0 1.25rem;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                overflow-x: hidden;
            }
            
            .header-inner {
                height: 82px;
                gap: 0.75rem;
            }
            .brand {
                gap: 0.85rem;
            }
            .brand-logo {
                width: 44px;
                height: 44px;
                border-radius: 12px;
            }
            .brand-title {
                font-size: 1.15rem;
                font-weight: 800;
                white-space: nowrap;
                line-height: 1.15;
            }
            .nav-links, .header-actions .btn {
                display: none !important;
            }
            .mobile-menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 46px;
                height: 46px;
                border-radius: 12px;
                border: 1px solid var(--border-color);
                background: #f8fafc;
                color: #0f172a;
                cursor: pointer;
                transition: all 0.2s var(--ease-out);
                flex-shrink: 0;
            }
            .mobile-menu-toggle:active {
                transform: scale(0.94);
                background: #f1f5f9;
            }
            .mobile-menu-toggle svg {
                width: 24px;
                height: 24px;
            }

            /* Mobile Hero Redesign */
            .hero {
                padding: 1.5rem 0 2rem;
                width: 100%;
                max-width: 100%;
                overflow-x: hidden;
                text-align: left;
                background: #f8fafc;
            }
            .hero-inner {
                grid-template-columns: 1fr;
                gap: 1.75rem;
                width: 100%;
                max-width: 100%;
            }
            .hero-content {
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                text-align: left;
            }
            .hero-card-frame {
                padding: 1.75rem 1.35rem;
                border-radius: 20px;
                box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.06);
            }
            .hero-title {
                font-size: clamp(1.45rem, 5.8vw, 1.85rem) !important;
                line-height: 1.25 !important;
                letter-spacing: -0.02em !important;
                margin-bottom: 0.75rem !important;
                word-break: break-word !important;
                overflow-wrap: anywhere !important;
                text-align: left !important;
                font-weight: 800 !important;
                color: #0f172a !important;
            }
            .hero-subtitle {
                font-size: 0.92rem !important;
                line-height: 1.6 !important;
                color: #475569 !important;
                letter-spacing: normal !important;
                margin-bottom: 1.5rem !important;
                word-break: break-word !important;
                overflow-wrap: anywhere !important;
                text-align: left !important;
                text-justify: none !important;
            }
            .hero-ctas {
                margin-bottom: 1.5rem !important;
            }
            .btn-hero-single {
                height: 50px !important;
                font-size: 0.96rem !important;
                border-radius: 12px !important;
            }
            .hero-card-trust {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.65rem !important;
                padding-top: 1.25rem !important;
            }
            .trust-item {
                font-size: 0.84rem !important;
                font-weight: 650 !important;
                text-align: left !important;
            }
            .terminal-preview-card {
                padding: 1.35rem;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                text-align: left;
                border-radius: 16px;
            }
            .preview-metric-grid {
                grid-template-columns: 1fr;
                gap: 0.6rem;
                width: 100%;
            }
            .preview-metric {
                padding: 0.85rem;
                width: 100%;
                box-sizing: border-box;
            }
            /* Sections on Mobile */
            .section {
                padding: 3.25rem 0;
                width: 100%;
                max-width: 100%;
                overflow-x: hidden;
            }
            .section-header {
                margin: 0 auto 2.25rem auto !important;
                width: 100% !important;
                max-width: 600px !important;
                text-align: center !important;
            }
            .section-tag {
                text-align: center !important;
                display: inline-block !important;
                font-size: 0.74rem !important;
                margin-bottom: 0.4rem !important;
            }
            .section-title {
                text-align: center !important;
                font-size: clamp(1.45rem, 5.8vw, 1.85rem) !important;
                line-height: 1.24 !important;
                letter-spacing: normal !important;
                margin-bottom: 0.65rem !important;
            }
            .section-desc {
                font-size: 0.94rem !important;
                line-height: 1.6 !important;
                text-align: center !important;
                margin: 0 auto !important;
                max-width: 540px !important;
            }
            .cards-grid-4, .cards-grid-3 {
                grid-template-columns: 1fr;
                gap: 1.15rem;
                width: 100%;
            }
            .feature-card, .step-card, .announcement-card {
                padding: 1.4rem;
                width: 100%;
                box-sizing: border-box;
                text-align: left;
                border-radius: 16px;
            }
            .feature-title, .step-title, .announcement-title {
                text-align: left;
                font-size: 1.05rem;
            }
            .feature-text, .step-text, .announcement-body {
                text-align: left;
                text-justify: none;
                font-size: 0.88rem;
                line-height: 1.55;
            }
            .faq-wrapper {
                text-align: left;
            }
            .faq-btn {
                padding: 1.1rem 1.25rem;
                font-size: 0.94rem;
                text-align: left;
            }
            .faq-body {
                text-align: left;
                text-justify: none;
                font-size: 0.88rem;
                line-height: 1.6;
            }
            .announcement-empty-card {
                flex-direction: column;
                align-items: center;
                padding: 1.75rem 1.25rem;
                gap: 1rem;
                text-align: center;
                border-radius: 16px;
                margin: 0 auto;
                max-width: 100%;
            }
            .empty-text-wrap {
                text-align: center;
            }
            .empty-title {
                text-align: center;
            }
            .empty-desc {
                text-align: center;
                text-justify: none;
            }
            .footer {
                text-align: left;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
                width: 100%;
                text-align: left;
            }
            .footer-links {
                text-align: left;
            }
            .footer-bottom {
                flex-direction: column;
                gap: 0.85rem;
                text-align: left;
                align-items: flex-start;
            }
            .advisory-bar {
                font-size: 0.76rem;
                padding: 0.45rem 0.75rem;
            }
            .advisory-link span {
                display: none;
            }
        }
    </style>
</head>
<body>

@php
    $broadcastNotice = \App\Support\SystemSettings::get('broadcast_notice');
    $latestAnnouncement = (isset($announcements) && $announcements->isNotEmpty()) ? $announcements->first() : null;
    $hasAdvisory = !empty($broadcastNotice) || !empty($latestAnnouncement);
    $advisoryBadge = !empty($broadcastNotice) ? 'Live Advisory' : 'Notice';
    $advisoryTitle = !empty($broadcastNotice) ? null : ($latestAnnouncement ? $latestAnnouncement->title : null);
    $advisoryBody = !empty($broadcastNotice) ? $broadcastNotice : ($latestAnnouncement ? $latestAnnouncement->message : null);
@endphp

<!-- TOP ADVISORY / ANNOUNCEMENT BANNER -->
@if($hasAdvisory)
    <div class="advisory-bar">
        <div class="container">
            <div class="advisory-content">
                <span class="advisory-badge">{{ $advisoryBadge }}</span>
                <span class="advisory-text">
                    @if($advisoryTitle)
                        <strong>{{ $advisoryTitle }}:</strong>
                    @endif
                    {{ $advisoryBody }}
                </span>
            </div>
            <a href="#announcements" class="advisory-link">
                <span>View Notices</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
@endif

<!-- HEADER / NAVIGATION -->
<header class="header" id="site-header">
    <div class="container">
        <div class="header-inner">
            <a href="#hero" class="brand">
                <img class="brand-logo" src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }} Logo">
                <div class="brand-text">
                    <span class="brand-title">{{ \App\Support\SystemSettings::brandName() }}</span>
                </div>
            </a>

            <nav class="nav-links">
                <a href="#about" class="nav-link-item">About</a>
                <a href="#services" class="nav-link-item">Services</a>
                <a href="#membership" class="nav-link-item">Membership</a>
                <a href="#activities" class="nav-link-item">Activities</a>
                <a href="#announcements" class="nav-link-item">Announcements</a>
                <a href="#faqs" class="nav-link-item">FAQs</a>
            </nav>

            <div class="header-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 13h6V4H4v9zm10 7h6v-9h-6v9zM4 21h6v-4H4v4zm10-17v4h6V4h-6z"/></svg>
                        <span>Dashboard</span>
                    </a>
                @endauth

                <button id="mobile-toggle-btn" type="button" class="mobile-menu-toggle" aria-label="Toggle Menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- MOBILE DRAWER BACKDROP & MENU -->
<div id="drawer-backdrop" class="mobile-drawer-backdrop"></div>
<div id="mobile-drawer" class="mobile-nav-drawer">
    <div>
        <div class="drawer-header">
            <div class="brand">
                <img class="brand-logo" src="{{ srh_logo_url() }}" alt="Logo" style="width:36px;height:36px;">
                <span class="brand-title" style="font-size:1.05rem;">{{ \App\Support\SystemSettings::brandName() }}</span>
            </div>
            <button id="drawer-close-btn" type="button" style="background:none;border:none;color:#64748b;cursor:pointer;padding:4px;" aria-label="Close Menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <ul class="drawer-links">
            <li><a href="#about" class="drawer-link-item">About Association</a></li>
            <li><a href="#services" class="drawer-link-item">Passenger Services</a></li>
            <li><a href="#membership" class="drawer-link-item">Driver Membership</a></li>
            <li><a href="#activities" class="drawer-link-item">TODA Activities</a></li>
            <li><a href="#announcements" class="drawer-link-item">Announcements</a></li>
            <li><a href="#faqs" class="drawer-link-item">FAQs</a></li>
        </ul>
    </div>
    @auth
        <div class="drawer-actions">
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg" style="width:100%;">Go to Dashboard</a>
        </div>
    @endauth
</div>

<!-- ==========================================
     FULL CONTINUOUS ONE-PAGE SCROLL LAYOUT
     ========================================== -->

<!-- 1. HERO SECTION -->
<section id="hero" class="hero">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-card-frame">
                <h1 class="hero-title">{{ $landing['hero_title'] ?? 'Safe, Verified, and Reliable Tricycle Transport for Santa Rosa Homes Residents' }}</h1>
                <p class="hero-subtitle">{{ $landing['hero_subtitle'] ?? 'Connecting homeowners, commuters, and verified TODA drivers in Santa Rosa Homes, Nueva Ecija. Transparent fares, digital dispatch, and trusted community service.' }}</p>

                <div class="hero-ctas">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-hero-single">
                            <span>Go to Dashboard</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-hero-single">
                            <span>Get Started</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endauth
                </div>

                <div class="hero-card-trust">
                    <div class="trust-item">
                        <span class="trust-icon-pill">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>Verified TODA Drivers</span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon-pill">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>Standard Barangay Fares</span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon-pill">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>Live GPS Dispatch</span>
                    </div>
                </div>
            </div>

            <!-- Hero Right Card: Live Terminal & Dispatch Status -->
            <div class="terminal-preview-card">
                <div class="preview-header">
                    <div class="preview-title">Terminal Station Status</div>
                </div>

                <div class="preview-metric-grid">
                    <div class="preview-metric">
                        <div class="metric-label">Operating Hours</div>
                        <div class="metric-val" style="font-size:0.95rem;">5:00 AM – 11:00 PM</div>
                    </div>
                    <div class="preview-metric">
                        <div class="metric-label">Dispatch Zone</div>
                        <div class="metric-val" style="font-size:0.95rem;">Santa Rosa Homes</div>
                    </div>
                </div>

                <div class="preview-metric-grid">
                    <div class="preview-metric">
                        <div class="metric-label">Driver Compliance</div>
                        <div class="metric-val" style="font-size:0.95rem; color:#10b981;">100% Vetted</div>
                    </div>
                    <div class="preview-metric">
                        <div class="metric-label">Discount Policy</div>
                        <div class="metric-val" style="font-size:0.95rem;">20% Student/Senior</div>
                    </div>
                </div>

                <div class="preview-station-info">
                    <strong>Main Gate Dispatch Booth</strong><br>
                    {{ $landing['terminal_location'] ?? 'Santa Rosa Homes Main Gate Terminal, Santa Rosa, Nueva Ecija' }}
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. ABOUT SECTION -->
<section id="about" class="section section-alt">
    <div class="container">
        <div class="section-header reveal-on-scroll">
            <span class="section-tag">About the Association</span>
            <h2 class="section-title">{{ $landing['about_title'] ?? 'Serving Santa Rosa Homes with Pride & Integrity' }}</h2>
            <p class="section-desc">{{ $landing['about_text'] ?? 'SRH LINK-TODA is the community-driven transport network dedicated to providing orderly, secure, and courteous tricycle transportation. Operating in close coordination with homeowners and local barangay officials, we ensure every driver is vetted, fares are regulated, and passenger safety comes first.' }}</p>
        </div>

        <div class="cards-grid-3">
            <div class="feature-card reveal-on-scroll stagger-1">
                <div class="feature-icon-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="feature-title">Community Safety First</h3>
                <p class="feature-text">Every tricycle driver is registered with a verified Barangay Clearance, LTO license, and official TODA body number for maximum passenger peace of mind.</p>
            </div>

            <div class="feature-card reveal-on-scroll stagger-2">
                <div class="feature-icon-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="feature-title">Standardized Fair Fares</h3>
                <p class="feature-text">No overcharging or guessing fares. Clear rate matrices are set in accordance with municipal ordinances, respecting discounts for students and seniors.</p>
            </div>

            <div class="feature-card reveal-on-scroll stagger-3">
                <div class="feature-icon-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="feature-title">Orderly Terminal Queue</h3>
                <p class="feature-text">Our digitized terminal queue ensures rapid passenger pickup without clutter at the subdivision gate, keeping neighborhood roads orderly.</p>
            </div>
        </div>
    </div>
</section>

<!-- 3. PASSENGER SERVICES SECTION -->
<section id="services" class="section">
    <div class="container">
        <div class="section-header reveal-on-scroll">
            <span class="section-tag">Passenger Features</span>
            <h2 class="section-title">Convenient Commuting Within Santa Rosa Homes</h2>
            <p class="section-desc">Designed specifically for residents, visitors, and daily commuters traveling between Santa Rosa Homes and nearby junction terminals.</p>
        </div>

        <div class="cards-grid-4">
            <div class="feature-card reveal-on-scroll stagger-1">
                <div class="feature-icon-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="feature-title">Digital Ride Request</h3>
                <p class="feature-text">Request a tricycle directly from your phone while inside your home, avoiding long waits under the sun.</p>
            </div>

            <div class="feature-card reveal-on-scroll stagger-2">
                <div class="feature-icon-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <h3 class="feature-title">Live Route Tracking</h3>
                <p class="feature-text">Follow your assigned driver's approach in real-time on the map with estimated arrival updates.</p>
            </div>

            <div class="feature-card reveal-on-scroll stagger-3">
                <div class="feature-icon-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="feature-title">Lost & Found Support</h3>
                <p class="feature-text">Left an item behind? Quick reporting connects directly with terminal marshals to trace your ride record.</p>
            </div>

            <div class="feature-card reveal-on-scroll stagger-4">
                <div class="feature-icon-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <h3 class="feature-title">Community Standards</h3>
                <p class="feature-text">Strict safety and vehicle quality compliance ensures every trip remains peaceful and reliable.</p>
            </div>
        </div>
    </div>
</section>

<!-- 4. DRIVER MEMBERSHIP SECTION -->
<section id="membership" class="section section-alt">
    <div class="container">
        <div class="section-header reveal-on-scroll">
            <span class="section-tag">Driver Membership</span>
            <h2 class="section-title">How to Become a TODA Member Driver</h2>
            <p class="section-desc">{{ $landing['membership_intro'] ?? 'Join an organized association committed to driver livelihood, safety standards, and orderly terminal queuing.' }}</p>
        </div>

        <div class="cards-grid-4">
            <div class="step-card reveal-on-scroll stagger-1">
                <span class="step-number">Step 01</span>
                <h3 class="step-title">{{ $landing['membership_step1_title'] ?? '1. Valid Driver\'s License' }}</h3>
                <p class="step-text">{{ $landing['membership_step1_desc'] ?? 'Submit a valid Professional or Non-Professional Driver\'s License issued by the LTO.' }}</p>
            </div>

            <div class="step-card reveal-on-scroll stagger-2">
                <span class="step-number">Step 02</span>
                <h3 class="step-title">{{ $landing['membership_step2_title'] ?? '2. Barangay & Police Clearance' }}</h3>
                <p class="step-text">{{ $landing['membership_step2_desc'] ?? 'Provide an updated Barangay Clearance and Police Clearance proving good community standing.' }}</p>
            </div>

            <div class="step-card reveal-on-scroll stagger-3">
                <span class="step-number">Step 03</span>
                <h3 class="step-title">{{ $landing['membership_step3_title'] ?? '3. MTOP & TODA Franchise' }}</h3>
                <p class="step-text">{{ $landing['membership_step3_desc'] ?? 'Verify your Motorized Tricycle Operator\'s Permit (MTOP) and official TODA franchise unit plate.' }}</p>
            </div>

            <div class="step-card reveal-on-scroll stagger-4">
                <span class="step-number">Step 04</span>
                <h3 class="step-title">{{ $landing['membership_step4_title'] ?? '4. Vehicle Inspection & Queue' }}</h3>
                <p class="step-text">{{ $landing['membership_step4_desc'] ?? 'Pass tricycle roadworthiness safety checks (brakes, lights, sidecar stability) and gain terminal dispatch queue access.' }}</p>
            </div>
        </div>

        @auth
            <div style="margin-top: 2.5rem;" class="reveal-on-scroll">
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Access Driver Portal</a>
            </div>
        @endauth
    </div>
</section>

<!-- 5. TODA ACTIVITIES SECTION -->
<section id="activities" class="section">
    <div class="container">
        <div class="section-header reveal-on-scroll">
            <span class="section-tag">Community & Welfare</span>
            <h2 class="section-title">TODA Activities & Mutual Aid</h2>
            <p class="section-desc">{{ $landing['activities_text'] ?? 'SRH LINK-TODA conducts regular defensive driving workshops, traffic safety assistance along subdivision gates, community clean-up drives, and manages an active mutual aid welfare fund for member families.' }}</p>
        </div>

        <div class="cards-grid-3">
            <div class="feature-card reveal-on-scroll stagger-1">
                <div class="feature-icon-wrap" style="background:#fef3c7; color:#d97706;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="feature-title">Road Safety Seminars</h3>
                <p class="feature-text">Regular workshops on defensive driving, local speed limits within residential zones, and courtesy standards for all members.</p>
            </div>

            <div class="feature-card reveal-on-scroll stagger-2">
                <div class="feature-icon-wrap" style="background:#ecfdf5; color:#059669;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="feature-title">Subdivision Clean-Up Drives</h3>
                <p class="feature-text">Active civic participation in maintaining cleanliness and clear roadways around the Santa Rosa Homes gate and terminal vicinity.</p>
            </div>

            <div class="feature-card reveal-on-scroll stagger-3">
                <div class="feature-icon-wrap" style="background:#ede9fe; color:#7c3aed;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="feature-title">Driver Mutual Aid Fund</h3>
                <p class="feature-text">Emergency medical and bereavement assistance programs to support members and their families during difficult times.</p>
            </div>
        </div>
    </div>
</section>

<!-- 6. ANNOUNCEMENTS & ADVISORIES SECTION -->
<section id="announcements" class="section section-alt">
    <div class="container">
        <div class="section-header reveal-on-scroll">
            <span class="section-tag">Community Notice</span>
            <h2 class="section-title">Latest Association Announcements</h2>
            <p class="section-desc">Official updates, terminal advisories, and service schedule notices.</p>
        </div>

        @if(isset($announcements) && $announcements->isNotEmpty())
            <div class="cards-grid-3">
                @foreach($announcements as $index => $item)
                    <div class="announcement-card reveal-on-scroll stagger-{{ ($index % 3) + 1 }}">
                        <div>
                            <div class="announcement-meta">
                                <span class="announcement-badge">{{ $item->target_audience ?? 'General' }}</span>
                                <span>{{ $item->created_at ? $item->created_at->format('M d, Y') : '' }}</span>
                            </div>
                            <h3 class="announcement-title">{{ $item->title }}</h3>
                            <p class="announcement-body">{{ $item->message }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="announcement-empty-card reveal-on-scroll">
                <div class="empty-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div class="empty-text-wrap">
                    <h3 class="empty-title">No Active Advisories or Announcements</h3>
                    <p class="empty-desc">All Santa Rosa Homes TODA transport operations and dispatch stations are running on normal schedule. Official notices, holiday schedules, and community advisories will be published here.</p>
                </div>
                <div class="empty-badge-status">
                    <span>All Systems Normal</span>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- 7. FAQS SECTION -->
<section id="faqs" class="section">
    <div class="container">
        <div class="section-header reveal-on-scroll">
            <span class="section-tag">Help & Information</span>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-desc">Got questions regarding rates, routes, membership, or safety? Find quick answers below.</p>
        </div>

        <div class="faq-wrapper reveal-on-scroll">
            @php $faqs = $landing['faqs'] ?? []; @endphp
            @foreach($faqs as $index => $faq)
                <div class="faq-item {{ $loop->first ? 'active' : '' }}">
                    <button type="button" class="faq-btn" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                        <span>{{ $faq['question'] ?? '' }}</span>
                        <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="faq-body">
                        {{ $faq['answer'] ?? '' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand-title">{{ \App\Support\SystemSettings::brandName() }}</div>
                <p class="footer-brand-desc">{{ \App\Support\SystemSettings::brandTagline() }}</p>
                <div style="font-size:0.75rem; color:#64748b;">
                    Official Community Transport Association &bull; Santa Rosa Homes, Nueva Ecija
                </div>
            </div>

            <div>
                <div class="footer-col-title">Navigation</div>
                <ul class="footer-links">
                    <li><a href="#about">About Association</a></li>
                    <li><a href="#services">Passenger Services</a></li>
                    <li><a href="#membership">Driver Membership</a></li>
                    <li><a href="#activities">Community Activities</a></li>
                    <li><a href="#announcements">Announcements</a></li>
                    <li><a href="#faqs">FAQs</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-col-title">Portals</div>
                <ul class="footer-links">
                    @auth
                        <li><a href="{{ route('dashboard') }}">Go to Dashboard</a></li>
                    @endauth
                    <li><a href="{{ route('superadmin.login') }}">Superadmin Portal</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-col-title">Contact & Support</div>
                <ul class="footer-links">
                    <li class="footer-contact-item">
                        <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <div>
                            <span class="footer-contact-label">Hotline:</span>
                            <span class="footer-contact-val">{{ $landing['dispatch_hotline'] ?? '+63 912 847 4813' }}</span>
                        </div>
                    </li>
                    <li class="footer-contact-item">
                        <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <div>
                            <span class="footer-contact-label">Email:</span>
                            <span class="footer-contact-val">{{ $landing['dispatch_email'] ?? 'dispatch@srh-link-toda.org' }}</span>
                        </div>
                    </li>
                    <li class="footer-contact-item">
                        <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <div>
                            <span class="footer-contact-label">Terminal:</span>
                            <span class="footer-contact-val">{{ $landing['terminal_location'] ?? 'Santa Rosa Homes Main Gate Terminal' }}</span>
                        </div>
                    </li>
                    <li class="footer-contact-item">
                        <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span class="footer-contact-label">Hours:</span>
                            <span class="footer-contact-val">{{ $landing['terminal_hours'] ?? '5:00 AM – 11:00 PM' }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div>&copy; {{ date('Y') }} {{ \App\Support\SystemSettings::brandName() }}. All rights reserved.</div>
            <div>Operating in coordination with the Homeowners Association and Local Barangay Officials.</div>
        </div>
    </div>
</footer>

<script>
    // Header shadow on scroll & instant reveal on any upward scroll
    const header = document.getElementById('site-header');
    let lastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;

    function onScrollUpdate() {
        const currentScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
        
        if (!header) return;

        // Shadow toggle
        if (currentScrollY > 10) {
            header.classList.add('header-scrolled');
        } else {
            header.classList.remove('header-scrolled');
        }

        const delta = currentScrollY - lastScrollY;

        // If near top (<= 50px), ALWAYS show header
        if (currentScrollY <= 50) {
            header.classList.remove('header-hidden');
        }
        // If scrolling UP (delta < 0), IMMEDIATELY reveal header
        else if (delta < 0) {
            header.classList.remove('header-hidden');
        }
        // If scrolling DOWN (> 5px) past 60px threshold, hide header
        else if (delta > 5 && currentScrollY > 60) {
            header.classList.add('header-hidden');
        }

        lastScrollY = Math.max(0, currentScrollY);
    }

    window.addEventListener('scroll', onScrollUpdate, { passive: true });
    window.addEventListener('touchmove', onScrollUpdate, { passive: true });
    window.addEventListener('wheel', onScrollUpdate, { passive: true });

    // FAQ Accordion Interactivity
    document.querySelectorAll('.faq-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const item = this.closest('.faq-item');
            const isOpen = item.classList.contains('active');
            
            // Close all
            document.querySelectorAll('.faq-item').forEach(function(i) {
                i.classList.remove('active');
                const b = i.querySelector('.faq-btn');
                if (b) b.setAttribute('aria-expanded', 'false');
            });
            
            // Toggle clicked
            if (!isOpen) {
                item.classList.add('active');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // Mobile Navigation Drawer Toggle & Smooth Scroll
    const menuBtn = document.getElementById('mobile-toggle-btn');
    const drawer = document.getElementById('mobile-drawer');
    const backdrop = document.getElementById('drawer-backdrop');
    const closeBtn = document.getElementById('drawer-close-btn');

    function openDrawer() {
        if (drawer) drawer.classList.add('open');
        if (backdrop) backdrop.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        if (drawer) drawer.classList.remove('open');
        if (backdrop) backdrop.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (menuBtn) menuBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (backdrop) backdrop.addEventListener('click', closeDrawer);

    // Smooth Anchor Scrolling with Clean URL (No #hash appended in address bar)
    document.querySelectorAll('a[href^="#"], .advisory-link').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href === '#') return;
            const targetEl = document.querySelector(href);
            if (targetEl) {
                e.preventDefault();
                closeDrawer();
                const headerEl = document.getElementById('site-header');
                const headerOffset = (headerEl ? headerEl.offsetHeight : 72) + 16;
                const elementPosition = targetEl.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: Math.max(0, offsetPosition),
                    behavior: 'smooth'
                });
                
                // Keep URL clean: remove/prevent directory #hash from displaying in address bar
                if (window.location.hash && window.history.replaceState) {
                    window.history.replaceState(null, document.title, window.location.pathname + window.location.search);
                }
            }
        });
    });

    // Handle initial page load if accessed via direct #hash link: smooth scroll and strip hash
    if (window.location.hash) {
        setTimeout(function() {
            const targetEl = document.querySelector(window.location.hash);
            if (targetEl) {
                const headerEl = document.getElementById('site-header');
                const headerOffset = (headerEl ? headerEl.offsetHeight : 72) + 16;
                const elementPosition = targetEl.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                window.scrollTo({
                    top: Math.max(0, offsetPosition),
                    behavior: 'smooth'
                });
            }
            // Strip hash from address bar immediately
            if (window.history.replaceState) {
                window.history.replaceState(null, document.title, window.location.pathname + window.location.search);
            }
        }, 120);
    }

    // High-Performance Intersection Observer for Modern Scroll Reveal Animations
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            rootMargin: '0px 0px -40px 0px',
            threshold: 0.08
        });

        document.querySelectorAll('.reveal-on-scroll').forEach(el => {
            revealObserver.observe(el);
        });
    } else {
        document.querySelectorAll('.reveal-on-scroll').forEach(el => {
            el.classList.add('is-visible');
        });
    }
</script>

</body>
</html>
