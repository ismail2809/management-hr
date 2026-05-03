<x-filament-panels::page>
    <style>
        /* ─── Carte de formulaire ─── */
        .gp-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 12px rgba(0,0,0,.03);
        }
        .dark .gp-card { background: #1e2535; border-color: #2d3f5a; }

        .gp-card-header {
            padding: 16px 22px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .dark .gp-card-header { border-color: #2d3f5a; }

        .gp-header-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            background: #0da8b1;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .gp-header-icon svg { width: 18px; height: 18px; stroke: white; fill: none; }

        .gp-card-title { font-size: 15px; font-weight: 700; color: #0f172a; }
        .dark .gp-card-title { color: #f1f5f9; }
        .gp-card-sub { font-size: 12px; color: #94a3b8; margin-top: 1px; }

        .gp-card-body { padding: 22px; }

        .gp-card-footer {
            padding: 14px 22px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .dark .gp-card-footer { background: #172030; border-color: #2d3f5a; }

        .gp-hint {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: #94a3b8;
        }
        .gp-hint svg { width: 14px; height: 14px; flex-shrink: 0; }

        .gp-submit {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 22px;
            border-radius: 9px;
            background: #0da8b1;
            color: white;
            font-size: 14px; font-weight: 600;
            border: none; cursor: pointer;
            transition: background .18s, transform .15s;
            box-shadow: 0 2px 10px rgba(13,168,177,.3);
        }
        .gp-submit:hover { background: #0b95a0; transform: translateY(-1px); }
        .gp-submit svg { width: 16px; height: 16px; }

        /* ─── Carte résultats ─── */
        .gp-results {
            animation: gp-appear .35s cubic-bezier(.22,1,.36,1);
        }
        @keyframes gp-appear {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Résumé compteurs */
        .gp-summary {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .gp-count {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }
        .gp-count-dot { width: 6px; height: 6px; border-radius: 50%; }

        /* Tableau */
        .gp-table { width: 100%; border-collapse: collapse; }
        .gp-table thead tr {
            border-bottom: 2px solid #f1f5f9;
        }
        .dark .gp-table thead tr { border-color: #2d3f5a; }
        .gp-table th {
            padding: 11px 18px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .7px;
            color: #94a3b8; text-align: left;
        }
        .gp-table td {
            padding: 12px 18px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            vertical-align: middle;
        }
        .dark .gp-table td { color: #cbd5e1; border-color: #253047; }
        .gp-table tbody tr:last-child td { border-bottom: none; }
        .gp-table tbody tr { transition: background .12s; }
        .gp-table tbody tr:hover td { background: #f8fafc; }
        .dark .gp-table tbody tr:hover td { background: #1a2a40; }

        /* Cellule employé avec initiales */
        .gp-emp {
            display: flex; align-items: center; gap: 10px;
        }
        .gp-emp-init {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: #e6f7f8;
            color: #0a7a82;
            font-size: 11px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .gp-emp-name { font-weight: 600; color: #0f172a; }
        .dark .gp-emp-name { color: #f1f5f9; }

        /* Badges statut */
        .gp-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .gp-badge svg { width: 11px; height: 11px; }

        /* État vide */
        .gp-empty {
            padding: 48px 24px;
            text-align: center;
        }
        .gp-empty svg { width: 44px; height: 44px; stroke: #cbd5e1; margin: 0 auto 12px; display: block; }
        .gp-empty-t { font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 5px; }
        .gp-empty-s { font-size: 13px; color: #94a3b8; }
    </style>

    {{-- ─── Formulaire ─── --}}
    <div class="gp-card">
        <div class="gp-card-header">
            <div class="gp-header-icon">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <div class="gp-card-title">Génération des bulletins de paie</div>
                <div class="gp-card-sub">Sélectionnez la période et les employés à traiter</div>
            </div>
        </div>

        <div class="gp-card-body">
            <form wire:submit="generate" id="gp-form">
                {{ $this->form }}
            </form>
        </div>

        <div class="gp-card-footer">
            <span class="gp-hint">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>
                </svg>
                Les bulletins déjà générés pour cette période seront ignorés
            </span>

            <button type="submit" form="gp-form" class="gp-submit">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span wire:loading.remove wire:target="generate">Lancer la génération</span>
                <span wire:loading wire:target="generate">Génération en cours…</span>
            </button>
        </div>
    </div>

    {{-- ─── Résultats ─── --}}
    @if ($generated)
    @php
        $nb_generes  = collect($results)->where('status', 'généré')->count();
        $nb_ignores  = collect($results)->where('status', 'ignoré')->count();
        $nb_erreurs  = collect($results)->where('status', 'erreur')->count();
    @endphp

    <div class="gp-card gp-results">
        <div class="gp-card-header">
            <div class="gp-header-icon" style="background:#059669;">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div style="flex:1;">
                <div class="gp-card-title">Résultats — {{ count($results) }} employé(s) traité(s)</div>
                <div class="gp-card-sub">Récapitulatif de la génération</div>
            </div>
            <div class="gp-summary">
                @if($nb_generes > 0)
                <span class="gp-count" style="background:#d1fae5;color:#065f46;">
                    <span class="gp-count-dot" style="background:#22c55e;"></span>
                    {{ $nb_generes }} généré{{ $nb_generes > 1 ? 's' : '' }}
                </span>
                @endif
                @if($nb_ignores > 0)
                <span class="gp-count" style="background:#fef3c7;color:#92400e;">
                    <span class="gp-count-dot" style="background:#f59e0b;"></span>
                    {{ $nb_ignores }} ignoré{{ $nb_ignores > 1 ? 's' : '' }}
                </span>
                @endif
                @if($nb_erreurs > 0)
                <span class="gp-count" style="background:#fee2e2;color:#991b1b;">
                    <span class="gp-count-dot" style="background:#ef4444;"></span>
                    {{ $nb_erreurs }} erreur{{ $nb_erreurs > 1 ? 's' : '' }}
                </span>
                @endif
            </div>
        </div>

        @if(count($results) > 0)
        <div style="overflow-x:auto;">
            <table class="gp-table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Statut</th>
                        <th>Détail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $row)
                    <tr>
                        <td>
                            <div class="gp-emp">
                                <div class="gp-emp-init">
                                    {{ strtoupper(mb_substr($row['name'], 0, 1)) }}{{ strtoupper(mb_substr(strrchr($row['name'], ' ') ?: $row['name'], 1, 1)) }}
                                </div>
                                <span class="gp-emp-name">{{ $row['name'] }}</span>
                            </div>
                        </td>
                        <td>
                            @if ($row['status'] === 'généré')
                                <span class="gp-badge" style="background:#d1fae5;color:#065f46;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Généré
                                </span>
                            @elseif ($row['status'] === 'ignoré')
                                <span class="gp-badge" style="background:#fef3c7;color:#92400e;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/></svg>
                                    Ignoré
                                </span>
                            @else
                                <span class="gp-badge" style="background:#fee2e2;color:#991b1b;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Erreur
                                </span>
                            @endif
                        </td>
                        <td style="color:#64748b;">{{ $row['detail'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="gp-empty">
            <svg fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <div class="gp-empty-t">Aucun résultat</div>
            <p class="gp-empty-s">Aucune fiche de paie n'a été traitée pour cette période.</p>
        </div>
        @endif
    </div>
    @endif

</x-filament-panels::page>
