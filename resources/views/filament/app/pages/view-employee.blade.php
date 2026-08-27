<x-filament-panels::page>
@php
    $emp = $this->record;
    $stats = $this->getLeaveStats();
    $statutFamilial = ['celibataire' => 'Célibataire', 'marie' => 'Marié(e)', 'divorce' => 'Divorcé(e)', 'veuf' => 'Veuf / Veuve'];
    $docs = $this->record->documents()->withoutGlobalScopes()->orderByDesc('created_at')->get();
    $conges = $emp->leaves()->withoutGlobalScopes()->orderByDesc('created_at')->limit(6)->get();
@endphp

<style>
    /* ─── Réinitialisation et base ─── */
    .ep * { box-sizing: border-box; }
    .ep { display: flex; flex-direction: column; gap: 20px; }

    /* ─── Carte générique ─── */
    .ep-card {
        background: white;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 12px rgba(0,0,0,.03);
    }
    .dark .ep-card { background: #1e2535; border-color: #2d3f5a; }

    .ep-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
    }
    .dark .ep-card-header { border-color: #2d3f5a; }
    .ep-card-title {
        font-size: 13px; font-weight: 700; color: #0f172a;
        display: flex; align-items: center; gap: 8px;
    }
    .dark .ep-card-title { color: #f1f5f9; }
    .ep-card-title-bar { width: 3px; height: 14px; border-radius: 2px; background: #0da8b1; flex-shrink: 0; }
    .ep-card-body { padding: 16px 20px; }

    /* ─── En-tête du profil ─── */
    .ep-profile {
        background: white;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 12px rgba(0,0,0,.03);
    }
    .dark .ep-profile { background: #1e2535; border-color: #2d3f5a; }

    .ep-profile-banner {
        height: 100px;
        background: linear-gradient(135deg, #0f172a 0%, #0d2444 55%, #0a7a82 100%);
        position: relative;
    }
    .ep-profile-banner::after {
        content: '';
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
        background-size: 36px 36px;
    }

    .ep-profile-body {
        padding: 0 24px 20px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ep-avatar-wrap {
        position: relative;
        margin-top: -40px;
    }
    .ep-avatar {
        width: 80px; height: 80px;
        border-radius: 50%;
        border: 3px solid white;
        background: #f1f5f9;
        overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 14px rgba(0,0,0,.1);
    }
    .dark .ep-avatar { border-color: #1e2535; }
    .ep-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .ep-avatar-initials { font-size: 24px; font-weight: 800; color: #0da8b1; }
    .ep-avatar-status {
        position: absolute; bottom: 3px; right: 3px;
        width: 14px; height: 14px; border-radius: 50%;
        border: 2px solid white;
    }
    .dark .ep-avatar-status { border-color: #1e2535; }

    .ep-profile-info { padding-top: 12px; }
    .ep-profile-name { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 3px; }
    .dark .ep-profile-name { color: #f1f5f9; }
    .ep-profile-role { font-size: 13px; color: #64748b; margin-bottom: 6px; }
    .ep-profile-meta { display: flex; gap: 16px; flex-wrap: wrap; }
    .ep-meta-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #94a3b8; }
    .ep-meta-item svg { width: 13px; height: 13px; flex-shrink: 0; }

    .ep-profile-badges { display: flex; gap: 8px; align-items: center; padding-top: 14px; flex-wrap: wrap; }

    /* ─── Statistiques congés ─── */
    .ep-kpi { display: grid; grid-template-columns: repeat(3, 1fr); border-top: 1px solid #f1f5f9; }
    .dark .ep-kpi { border-color: #2d3f5a; }
    .ep-kpi-cell { padding: 14px; text-align: center; border-right: 1px solid #f1f5f9; }
    .dark .ep-kpi-cell { border-color: #2d3f5a; }
    .ep-kpi-cell:last-child { border-right: none; }
    .ep-kpi-val { font-size: 26px; font-weight: 800; line-height: 1; }
    .ep-kpi-lbl { font-size: 10px; text-transform: uppercase; letter-spacing: .6px; color: #94a3b8; margin-top: 3px; }

    /* ─── Grille principale ─── */
    .ep-grid { display: grid; grid-template-columns: 280px 1fr; gap: 20px; align-items: start; }
    @media (max-width: 880px) { .ep-grid { grid-template-columns: 1fr; } }

    /* ─── Lignes d'info ─── */
    .ep-info-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 9px 0; border-bottom: 1px solid #f8fafc;
        font-size: 13px;
    }
    .dark .ep-info-row { border-color: #253047; }
    .ep-info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .ep-info-lbl { font-size: 12px; color: #94a3b8; }
    .ep-info-val { font-weight: 500; color: #0f172a; text-align: right; max-width: 55%; word-break: break-word; }
    .dark .ep-info-val { color: #e2e8f0; }

    /* ─── Pill / badge ─── */
    .ep-pill {
        display: inline-flex; align-items: center;
        padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
    }

    /* ─── Ligne document ─── */
    .ep-doc-row {
        display: flex; align-items: center; gap: 11px;
        padding: 11px 0; border-bottom: 1px solid #f8fafc;
    }
    .dark .ep-doc-row { border-color: #253047; }
    .ep-doc-row:last-child { border-bottom: none; padding-bottom: 0; }
    .ep-doc-icon {
        width: 36px; height: 36px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ep-doc-icon svg { width: 18px; height: 18px; }
    .ep-doc-name { font-size: 13px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dark .ep-doc-name { color: #e2e8f0; }
    .ep-doc-meta { font-size: 11px; color: #94a3b8; margin-top: 1px; }

    /* ─── Boutons ─── */
    .ep-btn {
        padding: 5px 11px; border-radius: 7px; font-size: 11px; font-weight: 600;
        border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: opacity .15s;
    }
    .ep-btn:hover { opacity: .85; }
    .ep-btn svg { width: 11px; height: 11px; }

    /* ─── Formulaire d'upload ─── */
    .ep-upload-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
    .ep-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #64748b; display: block; margin-bottom: 5px; }
    .ep-input {
        width: 100%; padding: 8px 11px;
        border: 1px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; color: #0f172a;
        background: white; outline: none;
        transition: border-color .18s;
    }
    .dark .ep-input { background: #253047; border-color: #334155; color: #e2e8f0; }
    .ep-input:focus { border-color: #0da8b1; box-shadow: 0 0 0 3px rgba(13,168,177,.1); }
    @error('documentName') .ep-input-name { border-color: #ef4444 !important; } @enderror
    @error('uploadedFile') .ep-input-file { border-color: #ef4444 !important; } @enderror

    .ep-submit {
        width: 100%; padding: 9px; border-radius: 8px;
        background: #0da8b1; color: white; font-size: 13px; font-weight: 700;
        border: none; cursor: pointer; transition: background .18s;
    }
    .ep-submit:hover { background: #0b95a0; }

    /* ─── Ligne congé ─── */
    .ep-leave-row {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 0; border-bottom: 1px solid #f8fafc;
    }
    .dark .ep-leave-row { border-color: #253047; }
    .ep-leave-row:last-child { border-bottom: none; }
    .ep-leave-icon {
        width: 32px; height: 32px; border-radius: 8px; background: #e6f7f8;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ep-leave-icon svg { width: 15px; height: 15px; stroke: #0da8b1; fill: none; }

    /* ─── Badge compteur ─── */
    .ep-count {
        font-size: 11px; font-weight: 700; color: #94a3b8;
        background: #f1f5f9; padding: 2px 8px; border-radius: 10px;
    }
    .dark .ep-count { background: #253047; }

    /* ─── État vide ─── */
    .ep-empty { text-align: center; padding: 28px 16px; color: #94a3b8; }
    .ep-empty svg { width: 38px; height: 38px; margin: 0 auto 8px; display: block; opacity: .3; }
    .ep-empty p { font-size: 13px; margin: 0; }
</style>

<div class="ep">

    {{-- ═══ EN-TÊTE PROFIL ═══ --}}
    <div class="ep-profile">
        <div class="ep-profile-banner"></div>

        <div class="ep-profile-body">
            <div style="display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap;">
                <div class="ep-avatar-wrap">
                    <div class="ep-avatar">
                        @if($emp->photo)
                            <img src="{{ Storage::url($emp->photo) }}" alt="{{ $emp->full_name }}">
                        @else
                            <span class="ep-avatar-initials">{{ strtoupper(mb_substr($emp->first_name, 0, 1)) }}{{ strtoupper(mb_substr($emp->last_name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="ep-avatar-status" style="background:{{ $emp->status === 'actif' ? '#22c55e' : '#94a3b8' }};"></div>
                </div>

                <div class="ep-profile-info">
                    <div class="ep-profile-name">{{ $emp->full_name }}</div>
                    <div class="ep-profile-role">{{ $emp->profession?->name ?? 'Poste non défini' }}</div>
                    <div class="ep-profile-meta">
                        @if($emp->matricule)
                        <span class="ep-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                            {{ $emp->matricule }}
                        </span>
                        @endif
                        @if($emp->hire_date)
                        <span class="ep-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Depuis {{ $emp->hire_date->format('d/m/Y') }}
                        </span>
                        @endif
                        @if($emp->email)
                        <span class="ep-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $emp->email }}
                        </span>
                        @endif
                        @if($emp->phone)
                        <span class="ep-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $emp->phone }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ep-profile-badges">
                @php
                    $ctColors = ['CDI' => ['#d1fae5','#065f46'], 'CDD' => ['#fef3c7','#92400e'], 'Stage' => ['#dbeafe','#1e40af'], 'ANAPEC' => ['#f1f5f9','#475569']];
                    $stColors = ['actif' => ['#d1fae5','#065f46'], 'inactif' => ['#fee2e2','#991b1b'], 'suspendu' => ['#fef3c7','#92400e']];
                    $ctc = $ctColors[$emp->contract_type] ?? ['#f1f5f9','#475569'];
                    $stc = $stColors[$emp->status] ?? ['#f1f5f9','#64748b'];
                @endphp
                <span class="ep-pill" style="background:{{ $ctc[0] }};color:{{ $ctc[1] }};">{{ $emp->contract_type ?? '—' }}</span>
                <span class="ep-pill" style="background:{{ $stc[0] }};color:{{ $stc[1] }};">{{ ucfirst($emp->status ?? '—') }}</span>
            </div>
        </div>

        {{-- Statistiques congés --}}
        <div class="ep-kpi">
            <div class="ep-kpi-cell">
                <div class="ep-kpi-val" style="color:#0da8b1;">{{ $stats['pris'] }}</div>
                <div class="ep-kpi-lbl">Jours pris</div>
            </div>
            <div class="ep-kpi-cell">
                <div class="ep-kpi-val" style="color:#f59e0b;">{{ $stats['en_attente'] }}</div>
                <div class="ep-kpi-lbl">En attente</div>
            </div>
            <div class="ep-kpi-cell">
                <div class="ep-kpi-val" style="color:#ef4444;">{{ $stats['refuses'] }}</div>
                <div class="ep-kpi-lbl">Refusés</div>
            </div>
        </div>
    </div>

    {{-- ═══ GRILLE PRINCIPALE ═══ --}}
    <div class="ep-grid">

        {{-- ─ Colonne gauche ─ --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Informations personnelles --}}
            <div class="ep-card">
                <div class="ep-card-header">
                    <span class="ep-card-title">
                        <span class="ep-card-title-bar"></span>
                        Informations personnelles
                    </span>
                </div>
                <div class="ep-card-body" style="padding-top:6px;padding-bottom:6px;">
                    <div class="ep-info-row">
                        <span class="ep-info-lbl">CIN</span>
                        <span class="ep-info-val">{{ $emp->cin ?? '—' }}</span>
                    </div>
                    <div class="ep-info-row">
                        <span class="ep-info-lbl">N° CNSS</span>
                        <span class="ep-info-val">{{ $emp->cnss_number ?? '—' }}</span>
                    </div>
                    <div class="ep-info-row">
                        <span class="ep-info-lbl">Date de naissance</span>
                        <span class="ep-info-val">{{ $emp->birth_date?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                    <div class="ep-info-row">
                        <span class="ep-info-lbl">Situation familiale</span>
                        <span class="ep-info-val">{{ $statutFamilial[$emp->marital_status] ?? '—' }}</span>
                    </div>
                    <div class="ep-info-row">
                        <span class="ep-info-lbl">Nombre d'enfants</span>
                        <span class="ep-info-val">{{ $emp->number_of_children ?? 0 }}</span>
                    </div>
                    @if($emp->city || $emp->address)
                    <div class="ep-info-row">
                        <span class="ep-info-lbl">Adresse</span>
                        <span class="ep-info-val">{{ trim(($emp->address ? $emp->address.', ' : '').$emp->city) ?: '—' }}</span>
                    </div>
                    @endif
                    @if($emp->rib)
                    <div class="ep-info-row">
                        <span class="ep-info-lbl">RIB</span>
                        <span class="ep-info-val" style="font-family:monospace;font-size:11px;">{{ $emp->rib }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Contrats --}}
            @if($emp->contracts && $emp->contracts->count() > 0)
            <div class="ep-card">
                <div class="ep-card-header">
                    <span class="ep-card-title">
                        <span class="ep-card-title-bar" style="background:#f59e0b;"></span>
                        Contrats
                    </span>
                    <span class="ep-count">{{ $emp->contracts->count() }}</span>
                </div>
                <div class="ep-card-body" style="padding-top:4px;padding-bottom:4px;">
                    @foreach($emp->contracts as $c)
                    <div class="ep-doc-row">
                        <div class="ep-doc-icon" style="background:#f0fdf4;">
                            <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="ep-doc-name">{{ $c->contract_type }}</div>
                            <div class="ep-doc-meta">
                                {{ $c->start_date?->format('d/m/Y') ?? '—' }}@if($c->end_date) → {{ $c->end_date->format('d/m/Y') }}@endif
                                &nbsp;·&nbsp; <strong style="color:#0da8b1;">{{ number_format($c->salary_base, 0, ',', ' ') }} MAD</strong>
                            </div>
                        </div>
                        <span class="ep-pill" style="background:{{ $c->status === 'actif' ? '#d1fae5' : '#f1f5f9' }};color:{{ $c->status === 'actif' ? '#065f46' : '#64748b' }};">{{ ucfirst($c->status) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ─ Colonne droite ─ --}}
        <div style="display:flex;flex-direction:column;gap:20px;">


            {{-- Documents --}}
            <div class="ep-card">
                <div class="ep-card-header">
                    <span class="ep-card-title">
                        <span class="ep-card-title-bar"></span>
                        Documents
                    </span>
                    <span class="ep-count" style="color:#0da8b1;background:#e6f7f8;">{{ $docs->count() }}</span>
                </div>
                <div class="ep-card-body" style="padding-top:4px;padding-bottom:4px;">
                    @forelse($docs as $doc)
                    @php $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION)); @endphp
                    <div class="ep-doc-row">
                        <div class="ep-doc-icon" style="background:{{ $ext === 'pdf' ? '#fee2e2' : (in_array($ext, ['jpg','jpeg','png','webp']) ? '#fef3c7' : '#f1f5f9') }};">
                            @if($ext === 'pdf')
                                <svg fill="#ef4444" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>
                            @elseif(in_array($ext, ['jpg','jpeg','png','webp']))
                                <svg fill="none" stroke="#d97706" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @else
                                <svg fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="ep-doc-name">{{ $doc->name }}</div>
                            <div class="ep-doc-meta">{{ $doc->file_size_human }} · {{ $doc->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div style="display:flex;gap:6px;flex-shrink:0;">
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="ep-btn" style="background:#e6f7f8;color:#0da8b1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Voir
                            </a>
                            <button wire:click="deleteDocument({{ $doc->id }})" wire:confirm="Supprimer ce document ?" class="ep-btn" style="background:#fee2e2;color:#ef4444;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="ep-empty">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p>Aucun document enregistré</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Historique des congés --}}
            @if($conges->count() > 0)
            <div class="ep-card">
                <div class="ep-card-header">
                    <span class="ep-card-title">
                        <span class="ep-card-title-bar" style="background:#f59e0b;"></span>
                        Historique des congés
                    </span>
                    <span class="ep-count" style="color:#f59e0b;background:#fef3c7;">{{ $conges->count() }}</span>
                </div>
                <div class="ep-card-body" style="padding-top:4px;padding-bottom:4px;">
                    @foreach($conges as $conge)
                    @php
                        $lc = ['approuvé' => ['#d1fae5','#065f46'], 'en_attente' => ['#fef3c7','#92400e'], 'refusé' => ['#fee2e2','#991b1b']];
                        $ls = $lc[$conge->status] ?? ['#f1f5f9','#64748b'];
                    @endphp
                    <div class="ep-leave-row">
                        <div class="ep-leave-icon">
                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-bottom:3px;">
                                <span class="ep-pill" style="background:{{ $ls[0] }};color:{{ $ls[1] }};">{{ $conge->leaveType?->name ?? 'Congé' }}</span>
                                <span class="ep-pill" style="background:{{ $ls[0] }};color:{{ $ls[1] }};opacity:.75;">{{ ucfirst($conge->status) }}</span>
                            </div>
                            <div style="font-size:11px;color:#94a3b8;">
                                {{ $conge->start_date?->format('d/m/Y') }} → {{ $conge->end_date?->format('d/m/Y') }}
                                @if($conge->start_date && $conge->end_date)
                                    · {{ $conge->start_date->diffInDays($conge->end_date) + 1 }} j
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- fin colonne droite --}}
    </div>{{-- fin grille --}}
</div>{{-- fin ep --}}
</x-filament-panels::page>
