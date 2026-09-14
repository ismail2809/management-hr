<x-filament-panels::page>
@php
    $emp    = $this->record;
    $stats  = $this->getLeaveStats();
    $docs      = $this->record->documents()->orderByDesc('created_at')->get();
    $accidents = $this->record->accidents()->orderByDesc('created_at')->get();
    $fondation = $this->record->fondationM6()->orderByDesc('created_at')->get();
    $credits   = $this->record->credits()->orderByDesc('created_at')->get();
    $creditTypeLabels = ['voiture' => 'Voiture', 'immobilier' => 'Immobilier', 'consommation' => 'Consommation', 'education' => 'Éducation', 'autre' => 'Autre'];

    $statutFamilialMap = ['celibataire' => 'Célibataire', 'marie' => 'Marié(e)', 'divorce' => 'Divorcé(e)', 'veuf' => 'Veuf / Veuve'];
    $profTypeMap       = ['permanent' => 'Permanent', 'stagiaire' => 'Stagiaire', 'vacataire' => 'Vacataire'];
    $contractColors    = ['CDI' => ['#d1fae5','#065f46'], 'CDD' => ['#fef3c7','#92400e'], 'Stage' => ['#dbeafe','#1e40af'], 'ANAPEC' => ['#f1f5f9','#475569']];
    $statusColors      = ['actif' => ['#d1fae5','#065f46'], 'inactif' => ['#fee2e2','#991b1b'], 'sorti' => ['#f1f5f9','#475569']];
    $ctc = $contractColors[$emp->contract_type] ?? ['#f1f5f9','#475569'];
    $stc = $statusColors[$emp->status]          ?? ['#f1f5f9','#64748b'];
@endphp

<style>
/* ── Reset ── */
.vp * { box-sizing: border-box; margin: 0; padding: 0; }
.vp { display: flex; flex-direction: column; gap: 20px; font-family: inherit; }

/* ── Profile hero ── */
.vp-hero { border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 2px 12px rgba(0,0,0,.05); background: white; }
.dark .vp-hero { background: #1e2535; border-color: #2d3f5a; }

.vp-banner {
    height: 110px;
    background: linear-gradient(135deg, #0f172a 0%, #0d2444 50%, #0a7a82 100%);
    position: relative; overflow: hidden;
}
.vp-banner::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 70% 50%, rgba(13,168,177,.25) 0%, transparent 60%);
}
.vp-banner::after {
    content: '';
    position: absolute; inset: 0;
    background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
    background-size: 40px 40px;
}

.vp-hero-body { padding: 0 24px 20px; }
.vp-hero-top { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 12px; }

.vp-avatar-wrap { position: relative; margin-top: -44px; }
.vp-avatar {
    width: 88px; height: 88px; border-radius: 50%;
    border: 4px solid white; background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,.12);
}
.dark .vp-avatar { border-color: #1e2535; }
.vp-avatar img { width: 100%; height: 100%; object-fit: cover; }
.vp-avatar-initials { font-size: 28px; font-weight: 800; color: #0da8b1; letter-spacing: -1px; }
.vp-avatar-dot {
    position: absolute; bottom: 4px; right: 4px;
    width: 16px; height: 16px; border-radius: 50%;
    border: 3px solid white;
}
.dark .vp-avatar-dot { border-color: #1e2535; }

.vp-name { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; line-height: 1.2; }
.dark .vp-name { color: #f1f5f9; }
.vp-role { font-size: 13px; color: #64748b; margin-bottom: 8px; }

.vp-meta { display: flex; flex-wrap: wrap; gap: 14px; }
.vp-meta-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #94a3b8; }
.vp-meta-item svg { width: 13px; height: 13px; flex-shrink: 0; }

.vp-badges { display: flex; gap: 7px; flex-wrap: wrap; align-items: center; padding-top: 14px; }
.vp-pill {
    display: inline-flex; align-items: center;
    padding: 4px 11px; border-radius: 20px;
    font-size: 11px; font-weight: 700; letter-spacing: .2px;
}

/* ── KPI bar ── */
.vp-kpi { display: grid; grid-template-columns: repeat(3,1fr); border-top: 1px solid #f1f5f9; }
.dark .vp-kpi { border-color: #2d3f5a; }
.vp-kpi-cell { padding: 16px; text-align: center; border-right: 1px solid #f1f5f9; }
.dark .vp-kpi-cell { border-color: #2d3f5a; }
.vp-kpi-cell:last-child { border-right: none; }
.vp-kpi-val { font-size: 28px; font-weight: 800; line-height: 1; }
.vp-kpi-lbl { font-size: 10px; text-transform: uppercase; letter-spacing: .7px; color: #94a3b8; margin-top: 4px; }

/* ── Layout ── */
.vp-layout { display: flex; flex-direction: column; gap: 20px; }

/* ── Card ── */
.vp-card { background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
.dark .vp-card { background: #1e2535; border-color: #2d3f5a; }

.vp-card-hd {
    padding: 13px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.dark .vp-card-hd { border-color: #2d3f5a; }
.vp-card-title { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: .5px; }
.dark .vp-card-title { color: #e2e8f0; }
.vp-card-title svg { width: 15px; height: 15px; color: #0da8b1; flex-shrink: 0; }
.vp-card-bd { padding: 16px 18px; }

/* ── Info grid (2-col key/val pairs) ── */
.vp-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.vp-info-item { padding: 10px 12px; border-bottom: 1px solid #f8fafc; border-right: 1px solid #f8fafc; }
.dark .vp-info-item { border-color: #253047; }
.vp-info-item:nth-child(2n) { border-right: none; }
.vp-info-item:nth-last-child(-n+2) { border-bottom: none; }
.vp-info-lbl { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-bottom: 3px; font-weight: 600; }
.vp-info-val { font-size: 13px; font-weight: 600; color: #0f172a; }
.dark .vp-info-val { color: #e2e8f0; }
.vp-info-val.empty { color: #cbd5e1; font-weight: 400; }
.vp-info-full { grid-column: span 2; }

/* ── Doc / leave rows ── */
.vp-row { display: flex; align-items: center; gap: 11px; padding: 11px 0; border-bottom: 1px solid #f8fafc; }
.dark .vp-row { border-color: #253047; }
.vp-row:last-child { border-bottom: none; padding-bottom: 0; }
.vp-row:first-child { padding-top: 0; }

.vp-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.vp-icon svg { width: 18px; height: 18px; }
.vp-row-name { font-size: 13px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dark .vp-row-name { color: #e2e8f0; }
.vp-row-meta { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* ── Buttons ── */
.vp-btn { padding: 5px 10px; border-radius: 7px; font-size: 11px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: opacity .15s; }
.vp-btn:hover { opacity: .8; }
.vp-btn svg { width: 11px; height: 11px; }

/* ── Upload form ── */
.vp-upload { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 14px; }
.dark .vp-upload { background: #253047; border-color: #334155; }
.vp-upload-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
.vp-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #64748b; display: block; margin-bottom: 4px; }
.vp-input { width: 100%; padding: 7px 10px; border: 1px solid #e2e8f0; border-radius: 7px; font-size: 12px; color: #0f172a; background: white; outline: none; transition: border-color .15s; }
.dark .vp-input { background: #1e2535; border-color: #334155; color: #e2e8f0; }
.vp-input:focus { border-color: #0da8b1; box-shadow: 0 0 0 3px rgba(13,168,177,.1); }
.vp-submit { width: 100%; padding: 8px; border-radius: 7px; background: #0da8b1; color: white; font-size: 12px; font-weight: 700; border: none; cursor: pointer; transition: background .15s; }
.vp-submit:hover { background: #0b95a0; }

/* ── Badge counter ── */
.vp-count { font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 10px; }

/* ── Empty state ── */
.vp-empty { text-align: center; padding: 28px 16px; color: #94a3b8; }
.vp-empty svg { width: 36px; height: 36px; margin: 0 auto 8px; display: block; opacity: .3; }
.vp-empty p { font-size: 12px; }

/* ── Column stacks ── */
.vp-stack { display: flex; flex-direction: column; gap: 16px; }

/* ── Tabs ── */
.vp-tabs { background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
.dark .vp-tabs { background: #1e2535; border-color: #2d3f5a; }
.vp-tab-bar { display: flex; border-bottom: 1px solid #f1f5f9; overflow-x: auto; scrollbar-width: none; }
.dark .vp-tab-bar { border-color: #2d3f5a; }
.vp-tab-bar::-webkit-scrollbar { display: none; }
.vp-tab-btn {
    flex-shrink: 0; padding: 13px 18px;
    font-size: 12px; font-weight: 600; color: #94a3b8;
    background: none; border: none; border-bottom: 2px solid transparent;
    cursor: pointer; display: flex; align-items: center; gap: 6px;
    transition: color .15s, border-color .15s; white-space: nowrap;
    margin-bottom: -1px;
}
.vp-tab-btn svg { width: 14px; height: 14px; flex-shrink: 0; }
.vp-tab-btn:hover { color: #0da8b1; }
.vp-tab-btn.active { color: #0da8b1; border-bottom-color: #0da8b1; }
.vp-tab-panel { display: none; }
.vp-tab-panel.active { display: block; }
</style>

<div class="vp">

{{-- ═══ HERO ═══ --}}
<div class="vp-hero">
    <div class="vp-banner"></div>
    <div class="vp-hero-body">
        <div class="vp-hero-top">
            <div style="display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap;">
                <div class="vp-avatar-wrap">
                    <div class="vp-avatar">
                        @php
                            $photoPath = $emp->photo
                                ?? $emp->documents->where('type_document', 'photo')->first()?->file_path;
                        @endphp
                        @if($photoPath)
                            <img src="{{ Storage::url($photoPath) }}" alt="{{ $emp->full_name }}">
                        @else
                            <span class="vp-avatar-initials">{{ strtoupper(mb_substr($emp->first_name,0,1)) }}{{ strtoupper(mb_substr($emp->last_name,0,1)) }}</span>
                        @endif
                    </div>
                    <div class="vp-avatar-dot" style="background:{{ $emp->status === 'actif' ? '#22c55e' : ($emp->status === 'inactif' ? '#ef4444' : '#94a3b8') }};"></div>
                </div>
                <div style="padding-top:12px;">
                    <div class="vp-name">{{ $emp->full_name }}</div>
                    <div class="vp-role">
                        {{ $emp->profession?->name ?? 'Poste non défini' }}
                        @if($emp->profession_type) · {{ $profTypeMap[$emp->profession_type] ?? $emp->profession_type }} @endif
                    </div>
                    <div class="vp-meta">
                        @if($emp->matricule)
                        <span class="vp-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                            Matricule {{ $emp->matricule }}
                        </span>
                        @endif
                        @if($emp->hire_date)
                        <span class="vp-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Depuis {{ $emp->hire_date->format('d/m/Y') }}
                        </span>
                        @endif
                        @if($emp->company)
                        <span class="vp-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $emp->company->name }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="vp-badges">
                @if($emp->contract_type)
                <span class="vp-pill" style="background:{{ $ctc[0] }};color:{{ $ctc[1] }};">{{ $emp->contract_type }}</span>
                @endif
                <span class="vp-pill" style="background:{{ $stc[0] }};color:{{ $stc[1] }};">{{ ucfirst($emp->status ?? '—') }}</span>
                @if($emp->gender)
                <span class="vp-pill" style="background:#f0f9ff;color:#0369a1;">{{ $emp->gender === 'M' ? 'Masculin' : 'Féminin' }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══ LAYOUT ═══ --}}
<div class="vp-layout">

    {{-- ── Colonne principale : Tabs ── --}}
    <div class="vp-tabs" x-data="{ tab: 'identite' }">

        {{-- Tab bar --}}
        <div class="vp-tab-bar">
            <button class="vp-tab-btn" :class="{ active: tab === 'identite' }" @click="tab = 'identite'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c0 1.657 1.343 3 3 3s3-1.343 3-3"/></svg>
                Identité
            </button>
            <button class="vp-tab-btn" :class="{ active: tab === 'coordonnees' }" @click="tab = 'coordonnees'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Coordonnées
            </button>
            <button class="vp-tab-btn" :class="{ active: tab === 'famille' }" @click="tab = 'famille'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                Situation familiale
            </button>
            <button class="vp-tab-btn" :class="{ active: tab === 'poste' }" @click="tab = 'poste'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Poste & Contrat
            </button>
            @if($emp->contracts && $emp->contracts->count() > 0)
            <button class="vp-tab-btn" :class="{ active: tab === 'contrats' }" @click="tab = 'contrats'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Contrats
                <span class="vp-count" style="background:#f0fdf4;color:#16a34a;">{{ $emp->contracts->count() }}</span>
            </button>
            @endif
            <button class="vp-tab-btn" :class="{ active: tab === 'documents' }" @click="tab = 'documents'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Documents
                <span class="vp-count" style="background:#e6f7f8;color:#0da8b1;">{{ $docs->count() }}</span>
            </button>
            <button class="vp-tab-btn" :class="{ active: tab === 'accidents' }" @click="tab = 'accidents'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Accidents de travail
                <span class="vp-count" style="background:#fee2e2;color:#ef4444;">{{ $accidents->count() }}</span>
            </button>
            <button class="vp-tab-btn" :class="{ active: tab === 'fondation' }" @click="tab = 'fondation'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                Fondation M6
                <span class="vp-count" style="background:#fef3c7;color:#d97706;">{{ $fondation->count() }}</span>
            </button>
            <button class="vp-tab-btn" :class="{ active: tab === 'credits' }" @click="tab = 'credits'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Crédits
                <span class="vp-count" style="background:#ede9fe;color:#7c3aed;">{{ $credits->count() }}</span>
            </button>
        </div>

        {{-- Panel : Identité --}}
        <div x-show="tab === 'identite'" class="vp-info-grid">
            <div class="vp-info-item">
                <div class="vp-info-lbl">CIN</div>
                <div class="vp-info-val {{ $emp->cin ? '' : 'empty' }}">{{ $emp->cin ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">N° CNSS</div>
                <div class="vp-info-val {{ $emp->cnss_number ? '' : 'empty' }}">{{ $emp->cnss_number ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Sexe</div>
                <div class="vp-info-val {{ $emp->gender ? '' : 'empty' }}">{{ $emp->gender === 'M' ? 'Masculin' : ($emp->gender === 'F' ? 'Féminin' : '—') }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Nationalité</div>
                <div class="vp-info-val {{ $emp->nationality ? '' : 'empty' }}">{{ $emp->nationality ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Date de naissance</div>
                <div class="vp-info-val {{ $emp->birth_date ? '' : 'empty' }}">{{ $emp->birth_date?->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Lieu de naissance</div>
                <div class="vp-info-val {{ $emp->birth_place ? '' : 'empty' }}">{{ $emp->birth_place ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Diplôme</div>
                <div class="vp-info-val {{ $emp->diploma ? '' : 'empty' }}">{{ $emp->diploma ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Promotion</div>
                <div class="vp-info-val {{ $emp->promotion ? '' : 'empty' }}">{{ $emp->promotion ?? '—' }}</div>
            </div>
            <div class="vp-info-item vp-info-full">
                <div class="vp-info-lbl">RIB</div>
                <div class="vp-info-val {{ $emp->rib ? '' : 'empty' }}" style="font-family:monospace;font-size:12px;letter-spacing:.5px;">{{ $emp->rib ?? '—' }}</div>
            </div>
        </div>

        {{-- Panel : Coordonnées --}}
        <div x-show="tab === 'coordonnees'" class="vp-info-grid">
            <div class="vp-info-item">
                <div class="vp-info-lbl">Email</div>
                <div class="vp-info-val {{ $emp->email ? '' : 'empty' }}" style="word-break:break-all;">{{ $emp->email ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Téléphone mobile</div>
                <div class="vp-info-val {{ $emp->phone ? '' : 'empty' }}">{{ $emp->phone ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Téléphone fixe</div>
                <div class="vp-info-val {{ $emp->phone_fixed ? '' : 'empty' }}">{{ $emp->phone_fixed ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Ville</div>
                <div class="vp-info-val {{ $emp->city ? '' : 'empty' }}">{{ $emp->city ?? '—' }}</div>
            </div>
            <div class="vp-info-item vp-info-full">
                <div class="vp-info-lbl">Adresse</div>
                <div class="vp-info-val {{ $emp->address ? '' : 'empty' }}">{{ $emp->address ?? '—' }}</div>
            </div>
        </div>

        {{-- Panel : Situation familiale --}}
        <div x-show="tab === 'famille'" class="vp-info-grid">
            <div class="vp-info-item">
                <div class="vp-info-lbl">État matrimonial</div>
                <div class="vp-info-val {{ $emp->marital_status ? '' : 'empty' }}">{{ $statutFamilialMap[$emp->marital_status] ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Nombre d'enfants</div>
                <div class="vp-info-val">{{ $emp->number_of_children ?? 0 }}</div>
            </div>
        </div>

        {{-- Panel : Poste & Contrat --}}
        <div x-show="tab === 'poste'" class="vp-info-grid">
            <div class="vp-info-item">
                <div class="vp-info-lbl">Profession</div>
                <div class="vp-info-val {{ $emp->profession ? '' : 'empty' }}">{{ $emp->profession?->name ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Type de profession</div>
                <div class="vp-info-val {{ $emp->profession_type ? '' : 'empty' }}">{{ $profTypeMap[$emp->profession_type] ?? '—' }}</div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Type de contrat</div>
                <div class="vp-info-val">
                    @if($emp->contract_type)
                        <span class="vp-pill" style="background:{{ $ctc[0] }};color:{{ $ctc[1] }};padding:2px 9px;">{{ $emp->contract_type }}</span>
                    @else
                        <span class="empty">—</span>
                    @endif
                </div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Statut</div>
                <div class="vp-info-val">
                    <span class="vp-pill" style="background:{{ $stc[0] }};color:{{ $stc[1] }};padding:2px 9px;">{{ ucfirst($emp->status ?? '—') }}</span>
                </div>
            </div>
            <div class="vp-info-item">
                <div class="vp-info-lbl">Date d'embauche</div>
                <div class="vp-info-val {{ $emp->hire_date ? '' : 'empty' }}">{{ $emp->hire_date?->format('d/m/Y') ?? '—' }}</div>
            </div>
            @if($emp->status === 'sorti' && $emp->exit_date)
            <div class="vp-info-item">
                <div class="vp-info-lbl">Date de sortie</div>
                <div class="vp-info-val">{{ $emp->exit_date?->format('d/m/Y') }}</div>
            </div>
            @if($emp->exit_reason)
            <div class="vp-info-item vp-info-full">
                <div class="vp-info-lbl">Motif de sortie</div>
                <div class="vp-info-val">{{ ucfirst($emp->exit_reason) }}</div>
            </div>
            @endif
            @endif
        </div>

        {{-- Panel : Contrats --}}
        @if($emp->contracts && $emp->contracts->count() > 0)
        <div x-show="tab === 'contrats'" class="vp-card-bd" style="padding:16px 18px;">
            @foreach($emp->contracts as $c)
            @php $cc = $contractColors[$c->contract_type] ?? ['#f1f5f9','#475569']; @endphp
            <div class="vp-row">
                <div class="vp-icon" style="background:#f0fdf4;">
                    <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="vp-row-name">{{ $c->contract_type }} — <strong style="color:#0da8b1;">{{ number_format($c->salary_base, 0, ',', ' ') }} MAD</strong></div>
                    <div class="vp-row-meta">{{ $c->start_date?->format('d/m/Y') ?? '—' }}@if($c->end_date) → {{ $c->end_date->format('d/m/Y') }}@endif</div>
                </div>
                <span class="vp-pill" style="background:{{ $c->status === 'actif' ? '#d1fae5' : '#f1f5f9' }};color:{{ $c->status === 'actif' ? '#065f46' : '#64748b' }};">{{ ucfirst($c->status) }}</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Panel : Documents --}}
        <div x-show="tab === 'documents'" style="padding:16px 18px;">
            @forelse($docs as $doc)
            @php $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION)); @endphp
            <div class="vp-row">
                <div class="vp-icon" style="background:{{ $ext === 'pdf' ? '#fee2e2' : (in_array($ext,['jpg','jpeg','png','webp']) ? '#fef3c7' : '#f1f5f9') }};">
                    @if($ext === 'pdf')
                        <svg fill="#ef4444" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>
                    @elseif(in_array($ext,['jpg','jpeg','png','webp']))
                        <svg fill="none" stroke="#d97706" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @else
                        <svg fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @endif
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="vp-row-name">{{ $doc->name }}</div>
                    <div class="vp-row-meta">{{ $doc->file_size_human }} · {{ $doc->created_at->format('d/m/Y') }}</div>
                </div>
                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="vp-btn" style="background:#e6f7f8;color:#0da8b1;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Télécharger
                </a>
            </div>
            @empty
            <p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px 0;">Aucun document</p>
            @endforelse
        </div>

        {{-- Panel : Accidents de travail --}}
        <div x-show="tab === 'accidents'" style="padding:16px 18px;">
            @forelse($accidents as $acc)
            <div class="vp-row">
                <div class="vp-icon" style="background:#fee2e2;">
                    <svg fill="none" stroke="#ef4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="vp-row-name" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span>{{ $acc->date_accident?->format('d/m/Y H:i') ?? '—' }}</span>
                        <span class="vp-pill" style="background:{{ $acc->rembourse ? '#d1fae5' : '#f1f5f9' }};color:{{ $acc->rembourse ? '#065f46' : '#64748b' }};">{{ $acc->rembourse ? 'Remboursé' : 'Non remboursé' }}</span>
                    </div>
                    @if($acc->montant)<div class="vp-row-meta">Montant : <strong>{{ number_format($acc->montant, 2, ',', ' ') }} MAD</strong></div>@endif
                    @if($acc->description)<div class="vp-row-meta">{{ $acc->description }}</div>@endif
                    @if($acc->dossiers)
                    <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;">
                        @foreach((array)$acc->dossiers as $file)
                        <a href="{{ Storage::url($file) }}" target="_blank" class="vp-btn" style="background:#fee2e2;color:#ef4444;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Dossier {{ $loop->iteration }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px 0;">Aucun accident enregistré</p>
            @endforelse
        </div>

        {{-- Panel : Fondation M6 --}}
        <div x-show="tab === 'fondation'" style="padding:16px 18px;">
            @forelse($fondation as $f)
            <div class="vp-row">
                <div class="vp-icon" style="background:#fef3c7;">
                    <svg fill="none" stroke="#d97706" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="vp-row-name">Année scolaire : <strong>{{ $f->annee_scolaire ?? '—' }}</strong></div>
                    @if($f->notes)<div class="vp-row-meta">{{ $f->notes }}</div>@endif
                    @if($f->fichiers)
                    <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;">
                        @foreach((array)$f->fichiers as $file)
                        <a href="{{ Storage::url($file) }}" target="_blank" class="vp-btn" style="background:#fef3c7;color:#d97706;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Fichier {{ $loop->iteration }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px 0;">Aucune entrée Fondation M6</p>
            @endforelse
        </div>

        {{-- Panel : Crédits --}}
        <div x-show="tab === 'credits'" style="padding:16px 18px;">
            @forelse($credits as $cr)
            <div class="vp-row">
                <div class="vp-icon" style="background:#ede9fe;">
                    <svg fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    @if($cr->types)
                    <div class="vp-row-name" style="display:flex;flex-wrap:wrap;gap:4px;">
                        @foreach((array)$cr->types as $t)
                        <span class="vp-pill" style="background:#ede9fe;color:#7c3aed;">{{ $creditTypeLabels[$t] ?? $t }}</span>
                        @endforeach
                    </div>
                    @endif
                    <div class="vp-row-meta" style="margin-top:4px;">
                        @if($cr->montant)Montant : <strong>{{ number_format($cr->montant, 2, ',', ' ') }} MAD</strong>@endif
                        @if($cr->montant && $cr->mensualite) &nbsp;·&nbsp; @endif
                        @if($cr->mensualite)Mensualité : <strong>{{ number_format($cr->mensualite, 2, ',', ' ') }} MAD</strong>@endif
                    </div>
                    @if($cr->notes)<div class="vp-row-meta">{{ $cr->notes }}</div>@endif
                </div>
            </div>
            @empty
            <p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px 0;">Aucun crédit enregistré</p>
            @endforelse
        </div>

    </div>{{-- fin tabs --}}

</div>{{-- fin layout --}}
</div>{{-- fin vp --}}
</x-filament-panels::page>
