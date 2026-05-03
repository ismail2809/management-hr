<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GestionRH — Simplifiez la paie de votre entreprise</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&family=inter:400,500" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --teal:       #0da8b1;
            --teal-hover: #0b95a0;
            --teal-soft:  #e6f7f8;
            --amber:      #f59e0b;
            --amber-soft: #fef3c7;
            --navy:       #0f172a;
            --body:       #334155;
            --muted:      #94a3b8;
            --border:     #e2e8f0;
            --surface:    #f8fafc;
            --white:      #ffffff;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--body);
            background: var(--white);
            line-height: 1.65;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--navy);
            line-height: 1.2;
        }

        a { text-decoration: none; color: inherit; }
        img { display: block; max-width: 100%; }

        /* ─── NAV ─────────────────────────────── */
        nav {
            position: sticky;
            top: 0;
            z-index: 50;
            height: 64px;
            padding: 0 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 9px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 18px;
            color: var(--navy);
        }
        .logo-mark {
            width: 34px; height: 34px;
            background: var(--teal);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
        }
        .logo-mark svg { width: 18px; height: 18px; fill: white; }

        .nav-ctas { display: flex; gap: 8px; align-items: center; }

        .btn { display: inline-flex; align-items: center; gap: 6px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600; font-size: 14px; border-radius: 8px; padding: 8px 18px; cursor: pointer; transition: all .18s; border: none; }
        .btn-outline { background: transparent; border: 1.5px solid var(--border); color: var(--navy); }
        .btn-outline:hover { border-color: var(--teal); color: var(--teal); }
        .btn-solid { background: var(--teal); color: white; box-shadow: 0 2px 10px rgba(13,168,177,.3); }
        .btn-solid:hover { background: var(--teal-hover); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(13,168,177,.38); }

        /* ─── HERO ─────────────────────────────── */
        .hero {
            padding: 80px 6% 100px;
            text-align: center;
            background: linear-gradient(180deg, var(--surface) 0%, var(--white) 100%);
            border-bottom: 1px solid var(--border);
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 14px;
            border-radius: 100px;
            background: var(--teal-soft);
            color: var(--teal-hover);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 28px;
            border: 1px solid rgba(13,168,177,.18);
        }
        .hero-pill span {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--teal);
        }

        .hero h1 {
            font-size: clamp(36px, 5vw, 62px);
            font-weight: 800;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
            max-width: 820px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero h1 em { font-style: normal; color: var(--teal); }

        .hero p {
            font-size: 18px;
            color: var(--muted);
            max-width: 520px;
            margin: 0 auto 40px;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 56px;
        }
        .btn-hero {
            padding: 14px 32px;
            font-size: 15px;
            border-radius: 10px;
            background: var(--teal);
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            box-shadow: 0 4px 20px rgba(13,168,177,.35);
            transition: all .2s;
            border: none;
            cursor: pointer;
        }
        .btn-hero:hover { background: var(--teal-hover); transform: translateY(-2px); box-shadow: 0 8px 28px rgba(13,168,177,.42); }

        .btn-hero-ghost {
            padding: 13px 28px;
            font-size: 15px;
            border-radius: 10px;
            background: white;
            color: var(--navy);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            border: 1.5px solid var(--border);
            cursor: pointer;
            transition: all .2s;
        }
        .btn-hero-ghost:hover { border-color: var(--teal); color: var(--teal); }

        /* ─── DASHBOARD PREVIEW ─────────────── */
        .hero-preview {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 80px rgba(15,23,42,.1), 0 4px 16px rgba(15,23,42,.05);
            overflow: hidden;
        }
        .preview-bar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .preview-dot { width: 10px; height: 10px; border-radius: 50%; }
        .preview-url {
            flex: 1;
            background: white;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 12px;
            color: var(--muted);
            margin: 0 12px;
        }
        .preview-body {
            display: grid;
            grid-template-columns: 200px 1fr;
            min-height: 340px;
        }
        .preview-sidebar {
            background: var(--navy);
            padding: 20px 14px;
        }
        .preview-sidebar-logo {
            display: flex; align-items: center; gap: 7px;
            margin-bottom: 24px;
        }
        .preview-sidebar-logo-mark {
            width: 26px; height: 26px;
            background: var(--teal);
            border-radius: 7px;
        }
        .preview-sidebar-logo span {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 700; color: white;
        }
        .preview-nav-item {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 12px; font-weight: 500;
            color: rgba(255,255,255,.5);
            margin-bottom: 3px;
        }
        .preview-nav-item.active { background: rgba(13,168,177,.2); color: var(--teal); }
        .preview-nav-item svg { width: 14px; height: 14px; flex-shrink: 0; }
        .preview-nav-dot { width: 14px; height: 14px; border-radius: 3px; background: currentColor; opacity: .5; flex-shrink: 0; }

        .preview-main { padding: 20px; background: var(--surface); }
        .preview-main-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; color: var(--navy); margin-bottom: 14px; }

        .preview-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px; }
        .preview-stat {
            background: white;
            border-radius: 10px;
            padding: 12px 14px;
            border: 1px solid var(--border);
        }
        .preview-stat-val { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 20px; font-weight: 800; }
        .preview-stat-lbl { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; margin-top: 2px; }

        .preview-table { background: white; border-radius: 10px; border: 1px solid var(--border); overflow: hidden; }
        .preview-table-header { display: grid; grid-template-columns: 1fr 80px 80px; gap: 8px; padding: 8px 12px; background: var(--surface); border-bottom: 1px solid var(--border); }
        .preview-table-header span { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); }
        .preview-table-row { display: grid; grid-template-columns: 1fr 80px 80px; gap: 8px; padding: 8px 12px; border-bottom: 1px solid var(--border); align-items: center; }
        .preview-table-row:last-child { border-bottom: none; }
        .preview-row-name { font-size: 11px; font-weight: 600; color: var(--navy); }
        .preview-row-dept { font-size: 10px; color: var(--muted); }
        .preview-badge { font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 10px; display: inline-block; }

        /* ─── STATS ─────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .stats-cell {
            padding: 48px 0;
            text-align: center;
            border-right: 1px solid var(--border);
        }
        .stats-cell:last-child { border-right: none; }
        .stats-val { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 42px; font-weight: 800; color: var(--navy); line-height: 1; }
        .stats-val span { color: var(--teal); }
        .stats-lbl { font-size: 14px; color: var(--muted); margin-top: 8px; }

        /* ─── SECTIONS ───────────────────────── */
        .section { padding: 96px 6%; }
        .section-inner { max-width: 1160px; margin: 0 auto; }
        .section-center { text-align: center; }

        .section-label {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--teal);
            margin-bottom: 14px;
            display: block;
        }
        .section-title {
            font-size: clamp(26px, 3vw, 40px);
            font-weight: 800;
            letter-spacing: -.8px;
            margin-bottom: 16px;
        }
        .section-desc {
            font-size: 17px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 540px;
        }
        .section-center .section-desc { margin: 0 auto; }

        /* ─── FEATURES ───────────────────────── */
        .features-bg { background: var(--surface); }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 56px;
        }
        .feature {
            background: white;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid var(--border);
            transition: transform .25s, box-shadow .25s;
        }
        .feature:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(15,23,42,.08); }
        .feat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 18px;
        }
        .feat-icon svg { width: 24px; height: 24px; }
        .feature h3 { font-size: 17px; font-weight: 700; margin-bottom: 9px; }
        .feature p { font-size: 14px; color: var(--muted); line-height: 1.65; }

        /* ─── CONFORMITE ─────────────────────── */
        .conform-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 72px;
            align-items: center;
            margin-top: 56px;
        }
        .conform-items { display: flex; flex-direction: column; gap: 16px; }
        .conform-card {
            display: flex; gap: 16px; align-items: flex-start;
            padding: 20px;
            background: var(--surface);
            border-radius: 14px;
            border: 1px solid var(--border);
            transition: border-color .2s;
        }
        .conform-card:hover { border-color: rgba(13,168,177,.35); }
        .conform-icon {
            width: 42px; height: 42px;
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .conform-icon svg { width: 22px; height: 22px; }
        .conform-card h4 { font-size: 14px; font-weight: 700; margin-bottom: 5px; }
        .conform-card p { font-size: 13px; color: var(--muted); line-height: 1.6; }

        .paie-card {
            background: white;
            border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: 0 16px 56px rgba(15,23,42,.09);
            overflow: hidden;
        }
        .paie-card-head {
            background: var(--navy);
            padding: 18px 22px;
        }
        .paie-card-head p { font-size: 12px; color: rgba(255,255,255,.5); margin-top: 2px; }
        .paie-card-head strong { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; color: white; }
        .paie-card-body { padding: 22px; }
        .paie-line {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            font-size: 13px;
            border-bottom: 1px solid var(--border);
        }
        .paie-line:last-of-type { border-bottom: none; }
        .paie-line .lbl { color: var(--muted); }
        .paie-line .val { font-weight: 600; color: var(--navy); }
        .paie-line .val.red { color: #ef4444; }
        .paie-total {
            margin-top: 14px;
            padding: 14px 16px;
            background: var(--teal-soft);
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(13,168,177,.2);
        }
        .paie-total span { font-size: 13px; font-weight: 700; color: var(--teal-hover); }
        .paie-total strong { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px; font-weight: 800; color: var(--teal); }

        /* ─── ETAPES ─────────────────────────── */
        .steps-bg { background: var(--surface); }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-top: 56px;
        }
        .step {
            text-align: center;
            padding: 0 8px;
        }
        .step-num {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--teal);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px; font-weight: 800; color: var(--teal);
            margin: 0 auto 18px;
        }
        .step h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .step p { font-size: 13px; color: var(--muted); line-height: 1.6; }

        /* ─── CTA ────────────────────────────── */
        .cta-section {
            background: var(--navy);
            padding: 96px 6%;
            text-align: center;
        }
        .cta-section h2 { color: white; font-size: clamp(28px, 3.5vw, 44px); font-weight: 800; letter-spacing: -.8px; margin-bottom: 14px; }
        .cta-section p { color: rgba(255,255,255,.55); font-size: 17px; max-width: 480px; margin: 0 auto 36px; line-height: 1.7; }
        .btn-amber {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 14px 32px;
            border-radius: 10px;
            background: var(--amber);
            color: var(--navy);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px; font-weight: 700;
            box-shadow: 0 4px 20px rgba(245,158,11,.35);
            transition: all .2s;
            border: none; cursor: pointer;
        }
        .btn-amber:hover { background: #e08e09; transform: translateY(-2px); box-shadow: 0 8px 28px rgba(245,158,11,.42); }

        /* ─── FOOTER ─────────────────────────── */
        footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 48px 6%;
        }
        .footer-inner {
            max-width: 1160px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 24px;
        }
        .footer-copy { font-size: 13px; color: var(--muted); }
        .footer-links { display: flex; gap: 24px; }
        .footer-links a { font-size: 13px; color: var(--muted); transition: color .15s; }
        .footer-links a:hover { color: var(--teal); }

        /* ─── RESPONSIVE ─────────────────────── */
        @media (max-width: 1024px) {
            .preview-body { grid-template-columns: 1fr; }
            .preview-sidebar { display: none; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .conform-grid { grid-template-columns: 1fr; }
            .paie-card { display: none; }
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .stats-cell { border-bottom: 1px solid var(--border); }
        }
        @media (max-width: 640px) {
            nav { padding: 0 4%; }
            .hero, .section, .cta-section { padding-left: 4%; padding-right: 4%; }
            .features-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: 1fr; }
            .stats-cell { border-right: none; }
            .hero h1 { letter-spacing: -1px; }
            .nav-links-center { display: none; }
        }
    </style>
</head>
<body>

<!-- ══════════════ NAVIGATION ══════════════ -->
<nav>
    <div class="logo">
        <div class="logo-mark">
            <svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM4 13v-2h2v2H4zm14 0v-2h2v2h-2zM4 9h16v1H4V9z"/></svg>
        </div>
        GestionRH
    </div>

    <div class="nav-ctas">
        @auth
            <a href="{{ url('/app') }}" class="btn btn-solid">Tableau de bord</a>
        @else
            <a href="{{ route('filament.app.auth.login') }}" class="btn btn-outline">Connexion</a>
            <a href="{{ route('filament.app.auth.login') }}" class="btn btn-solid">Essai gratuit</a>
        @endauth
    </div>
</nav>

<!-- ══════════════ HERO ══════════════════ -->
<section class="hero">
    <div class="hero-pill">
        <span></span>
        Conforme CNSS · AMO · IR 2025
    </div>

    <h1>La gestion RH des PME <em>marocaines</em>, simplifiée</h1>

    <p>Calculez la paie, gérez les congés et générez vos déclarations CNSS et IR en quelques clics. Zéro tableur, zéro erreur.</p>

    <div class="hero-actions">
        @auth
            <a href="{{ url('/app') }}" class="btn-hero">Accéder à mon espace</a>
        @else
            <a href="{{ route('filament.app.auth.login') }}" class="btn-hero">Démarrer gratuitement</a>
            <a href="#fonctionnalites" class="btn-hero-ghost">Voir les fonctionnalités</a>
        @endauth
    </div>

    <!-- Aperçu du tableau de bord -->
    <div class="hero-preview">
        <div class="preview-bar">
            <div class="preview-dot" style="background:#ef4444;"></div>
            <div class="preview-dot" style="background:#f59e0b;"></div>
            <div class="preview-dot" style="background:#22c55e;"></div>
            <div class="preview-url">app.gestionrh.ma/app</div>
        </div>
        <div class="preview-body">
            <div class="preview-sidebar">
                <div class="preview-sidebar-logo">
                    <div class="preview-sidebar-logo-mark"></div>
                    <span>GestionRH</span>
                </div>
                <div class="preview-nav-item active">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Tableau de bord
                </div>
                <div class="preview-nav-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Employés
                </div>
                <div class="preview-nav-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Paie
                </div>
                <div class="preview-nav-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Congés
                </div>
                <div class="preview-nav-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Déclarations
                </div>
            </div>
            <div class="preview-main">
                <div class="preview-main-title">Tableau de bord — Mai 2025</div>
                <div class="preview-stats">
                    <div class="preview-stat">
                        <div class="preview-stat-val" style="color:#0da8b1;">32</div>
                        <div class="preview-stat-lbl">Employés actifs</div>
                    </div>
                    <div class="preview-stat">
                        <div class="preview-stat-val" style="color:#f59e0b;">4</div>
                        <div class="preview-stat-lbl">Congés en attente</div>
                    </div>
                    <div class="preview-stat">
                        <div class="preview-stat-val" style="color:#22c55e;">100%</div>
                        <div class="preview-stat-lbl">Paie générée</div>
                    </div>
                </div>
                <div class="preview-table">
                    <div class="preview-table-header">
                        <span>Employé</span>
                        <span>Contrat</span>
                        <span>Salaire net</span>
                    </div>
                    <div class="preview-table-row">
                        <div>
                            <div class="preview-row-name">Salma Kabbaj</div>
                            <div class="preview-row-dept">Direction</div>
                        </div>
                        <span class="preview-badge" style="background:#d1fae5;color:#065f46;">CDI</span>
                        <span style="font-size:12px;font-weight:700;color:#0da8b1;">7 190 MAD</span>
                    </div>
                    <div class="preview-table-row">
                        <div>
                            <div class="preview-row-name">Omar Mansouri</div>
                            <div class="preview-row-dept">Comptabilité</div>
                        </div>
                        <span class="preview-badge" style="background:#d1fae5;color:#065f46;">CDI</span>
                        <span style="font-size:12px;font-weight:700;color:#0da8b1;">5 340 MAD</span>
                    </div>
                    <div class="preview-table-row">
                        <div>
                            <div class="preview-row-name">Nadia Benali</div>
                            <div class="preview-row-dept">Enseignement</div>
                        </div>
                        <span class="preview-badge" style="background:#fef3c7;color:#92400e;">CDD</span>
                        <span style="font-size:12px;font-weight:700;color:#0da8b1;">4 100 MAD</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ CHIFFRES ═════════════ -->
<div class="stats-row">
    <div class="stats-cell">
        <div class="stats-val">200<span>+</span></div>
        <div class="stats-lbl">Entreprises clientes</div>
    </div>
    <div class="stats-cell">
        <div class="stats-val">15<span>K</span></div>
        <div class="stats-lbl">Fiches de paie générées</div>
    </div>
    <div class="stats-cell">
        <div class="stats-val">99<span>%</span></div>
        <div class="stats-lbl">Conformité CNSS & IR</div>
    </div>
    <div class="stats-cell">
        <div class="stats-val">4<span>h</span></div>
        <div class="stats-lbl">Économisées par mois</div>
    </div>
</div>

<!-- ══════════════ FONCTIONNALITES ══════ -->
<section class="section features-bg" id="fonctionnalites">
    <div class="section-inner">
        <div class="section-center">
            <span class="section-label">Fonctionnalités</span>
            <h2 class="section-title">Tout ce dont votre service RH a besoin</h2>
            <p class="section-desc">Un seul outil pour gérer l'ensemble du cycle de vie de vos collaborateurs.</p>
        </div>

        <div class="features-grid">
            <div class="feature">
                <div class="feat-icon" style="background:#e6f7f8;">
                    <svg fill="none" stroke="#0da8b1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3>Gestion des employés</h3>
                <p>Fiches complètes : CIN, CNSS, RIB, contrats, historique et documents. Organisé par département et par poste.</p>
            </div>

            <div class="feature">
                <div class="feat-icon" style="background:#fef3c7;">
                    <svg fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3>Paie automatisée</h3>
                <p>Calcul CNSS, AMO et IR en un clic selon le barème marocain. Primes, retenues et bulletins PDF disponibles immédiatement.</p>
            </div>

            <div class="feature">
                <div class="feat-icon" style="background:#f0fdf4;">
                    <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3>Gestion des congés</h3>
                <p>Demandes, approbations et soldes en temps réel. Types de congés légaux marocains : annuel, maladie, maternité, sans solde.</p>
            </div>

            <div class="feature">
                <div class="feat-icon" style="background:#f5f3ff;">
                    <svg fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3>Suivi de présence</h3>
                <p>Enregistrement des pointages, calcul automatique des heures travaillées et des heures supplémentaires par mois.</p>
            </div>

            <div class="feature">
                <div class="feat-icon" style="background:#fef2f2;">
                    <svg fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3>Documents RH</h3>
                <p>Attestations de travail, de salaire, CNSS, ordre de mission et certificat de travail générés automatiquement en PDF.</p>
            </div>

            <div class="feature">
                <div class="feat-icon" style="background:#eff6ff;">
                    <svg fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3>Déclarations légales</h3>
                <p>Générez et archivez vos déclarations CNSS, IR mensuel et État 9421 directement depuis l'application.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ CONFORMITE ════════════ -->
<section class="section" id="conformite">
    <div class="section-inner">
        <div class="conform-grid">
            <div>
                <span class="section-label">Conformité Maroc</span>
                <h2 class="section-title">Taux légaux intégrés et à jour</h2>
                <p class="section-desc" style="margin-bottom:32px;">Plus besoin de vérifier les taux sur le site de la CNSS ou de la DGI. GestionRH intègre toutes les règles marocaines et les met à jour automatiquement.</p>

                <div class="conform-items">
                    <div class="conform-card">
                        <div class="conform-icon" style="background:#e6f7f8;">
                            <svg fill="none" stroke="#0da8b1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h4>CNSS — Caisse Nationale de Sécurité Sociale</h4>
                            <p>Salarié 4,48 % (plafond 6 000 MAD) + patronal 10,77 %. Déclaration mensuelle prête à soumettre.</p>
                        </div>
                    </div>
                    <div class="conform-card">
                        <div class="conform-icon" style="background:#fef3c7;">
                            <svg fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h4>AMO — Assurance Maladie Obligatoire</h4>
                            <p>Salarié 2,26 % + patronal 4,11 %. Intégré automatiquement dans le calcul de la paie nette.</p>
                        </div>
                    </div>
                    <div class="conform-card">
                        <div class="conform-icon" style="background:#f0fdf4;">
                            <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h4>IR — Impôt sur le Revenu (barème progressif DGI)</h4>
                            <p>Calcul selon le barème progressif + déduction forfaitaire frais professionnels 20 % (plafond 30 000 MAD/an).</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="paie-card">
                <div class="paie-card-head">
                    <strong>Simulation de paie</strong>
                    <p>Employé CDI · Salaire brut : 8 500 MAD</p>
                </div>
                <div class="paie-card-body">
                    <div class="paie-line">
                        <span class="lbl">Salaire brut</span>
                        <span class="val">8 500,00 MAD</span>
                    </div>
                    <div class="paie-line">
                        <span class="lbl">CNSS salarié (4,48 %)</span>
                        <span class="val red">− 268,80 MAD</span>
                    </div>
                    <div class="paie-line">
                        <span class="lbl">AMO salarié (2,26 %)</span>
                        <span class="val red">− 192,10 MAD</span>
                    </div>
                    <div class="paie-line">
                        <span class="lbl">Base IR (déd. frais pro 20 %)</span>
                        <span class="val">6 431,28 MAD</span>
                    </div>
                    <div class="paie-line">
                        <span class="lbl">IR (barème progressif)</span>
                        <span class="val red">− 848,54 MAD</span>
                    </div>
                    <div class="paie-total">
                        <span>Salaire net à payer</span>
                        <strong>7 190,56 MAD</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ ETAPES ════════════════ -->
<section class="section steps-bg" id="comment">
    <div class="section-inner">
        <div class="section-center">
            <span class="section-label">Prise en main</span>
            <h2 class="section-title">Opérationnel en moins d'une heure</h2>
            <p class="section-desc">Pas de formation longue, pas de consultant. GestionRH est pensé pour être pris en main immédiatement par votre équipe RH.</p>
        </div>

        <div class="steps-grid">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Créez votre compte</h3>
                <p>Renseignez les informations légales de votre entreprise (ICE, RC, affiliation CNSS).</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Ajoutez vos employés</h3>
                <p>Saisissez les informations contractuelles et bancaires (RIB) de chaque collaborateur.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Lancez la paie</h3>
                <p>En un clic, CNSS, AMO et IR sont calculés pour tous vos employés. Les bulletins PDF sont générés automatiquement.</p>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <h3>Générez vos déclarations</h3>
                <p>Exportez vos déclarations CNSS et IR prêtes à soumettre aux organismes compétents.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ CTA ═══════════════════ -->
<section class="cta-section">
    <h2>Simplifiez votre gestion RH dès aujourd'hui</h2>
    <p>Rejoignez les PME marocaines qui font confiance à GestionRH pour leur paie, leurs congés et leurs déclarations légales.</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        @auth
            <a href="{{ url('/app') }}" class="btn-amber">Accéder à mon espace RH</a>
        @else
            <a href="{{ route('filament.app.auth.login') }}" class="btn-amber">Créer un compte gratuit</a>
            <a href="{{ route('filament.app.auth.login') }}" style="display:inline-flex;align-items:center;padding:14px 28px;border-radius:10px;font-family:'Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:600;color:rgba(255,255,255,.7);border:1.5px solid rgba(255,255,255,.2);transition:all .2s;" onmouseover="this.style.color='white';this.style.borderColor='rgba(255,255,255,.5)'" onmouseout="this.style.color='rgba(255,255,255,.7)';this.style.borderColor='rgba(255,255,255,.2)'">Se connecter</a>
        @endauth
    </div>
</section>

<!-- ══════════════ FOOTER ════════════════ -->
<footer>
    <div class="footer-inner">
        <div class="logo">
            <div class="logo-mark">
                <svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM4 13v-2h2v2H4zm14 0v-2h2v2h-2zM4 9h16v1H4V9z"/></svg>
            </div>
            GestionRH
        </div>
        <div class="footer-copy">&copy; {{ date('Y') }} GestionRH · Tous droits réservés · Conçu au Maroc</div>
        <div class="footer-links">
            <a href="#fonctionnalites">Fonctionnalités</a>
            <a href="#conformite">Conformité</a>
            <a href="{{ route('filament.app.auth.login') }}">Connexion</a>
        </div>
    </div>
</footer>

</body>
</html>
