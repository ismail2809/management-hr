<x-filament-panels::page>

    @php
        $user          = auth()->user();
        $employee      = $user->employee;
        $balances      = $this->getLeaveBalances();
        $pending       = $this->getPendingLeaves();
        $lastPayroll   = \App\Models\Payroll::withoutGlobalScopes()
            ->where('employee_id', $user->employee_id)
            ->where('status', 'payé')
            ->orderByDesc('year')->orderByDesc('month')
            ->first();
        $bulletinCount = \App\Models\Payroll::withoutGlobalScopes()
            ->where('employee_id', $user->employee_id)
            ->where('status', 'payé')
            ->count();
    @endphp

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div style="background: linear-gradient(135deg, #0da8b1, #0891b2); border-radius: 16px; padding: 24px; color: white; margin-bottom: 24px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 700; flex-shrink: 0; border: 2px solid rgba(255,255,255,0.3);">
            @if($employee?->photo)
                <img src="{{ Storage::url($employee->photo) }}" style="width:56px;height:56px;border-radius:50%;object-fit:cover;" alt="">
            @else
                {{ strtoupper(substr($employee?->first_name ?? $user->name, 0, 1)) }}
            @endif
        </div>
        <div style="flex:1; min-width:0;">
            <div style="font-size:13px; opacity:.7;">Bienvenue,</div>
            <div style="font-size:20px; font-weight:700;">{{ $employee?->full_name ?? $user->name }}</div>
            <div style="font-size:13px; opacity:.6; margin-top:2px;">
                {{ implode(' · ', array_filter([$employee?->position?->title, $employee?->department?->name])) }}
            </div>
        </div>
        @if($employee?->matricule)
        <div style="text-align:right; flex-shrink:0;">
            <div style="font-size:11px; opacity:.6;">Matricule</div>
            <div style="font-size:17px; font-weight:600;">{{ $employee->matricule }}</div>
        </div>
        @endif
    </div>

    {{-- ── Stats cards ─────────────────────────────────────────────── --}}
    <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:16px; margin-bottom:24px;">

        <div style="background:var(--fi-bg); border:1px solid var(--fi-gray-200); border-radius:12px; padding:20px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--fi-gray-400); margin-bottom:8px;">Dernier salaire net</div>
            <div style="font-size:24px; font-weight:700; color:#10b981;">
                {{ $lastPayroll ? number_format($lastPayroll->salaire_net, 0, ',', ' ') . ' MAD' : '—' }}
            </div>
            <div style="font-size:12px; color:var(--fi-gray-400); margin-top:4px;">
                {{ $lastPayroll ? $lastPayroll->periode_label : 'Aucun bulletin' }}
            </div>
        </div>

        <div style="background:var(--fi-bg); border:1px solid var(--fi-gray-200); border-radius:12px; padding:20px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--fi-gray-400); margin-bottom:8px;">Bulletins disponibles</div>
            <div style="font-size:24px; font-weight:700; color:#0da8b1;">{{ $bulletinCount }}</div>
            <div style="font-size:12px; color:var(--fi-gray-400); margin-top:4px;">bulletin(s) payé(s)</div>
        </div>

        <div style="background:var(--fi-bg); border:1px solid var(--fi-gray-200); border-radius:12px; padding:20px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--fi-gray-400); margin-bottom:8px;">Congés en attente</div>
            <div style="font-size:24px; font-weight:700; color:{{ count($pending) > 0 ? '#fda31a' : '#9ca3af' }};">{{ count($pending) }}</div>
            <div style="font-size:12px; color:var(--fi-gray-400); margin-top:4px;">demande(s) en cours</div>
        </div>

        <div style="background:var(--fi-bg); border:1px solid var(--fi-gray-200); border-radius:12px; padding:20px;">
            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--fi-gray-400); margin-bottom:8px;">Ancienneté</div>
            <div style="font-size:24px; font-weight:700; color:var(--fi-gray-900);">
                {{ $employee?->hire_date ? $employee->hire_date->diffInYears(now()) : '—' }}
            </div>
            <div style="font-size:12px; color:var(--fi-gray-400); margin-top:4px;">
                {{ $employee?->hire_date ? 'an(s) depuis ' . $employee->hire_date->format('m/Y') : '' }}
            </div>
        </div>
    </div>

    {{-- ── Contenu principal ───────────────────────────────────────── --}}
    <div style="display:grid; grid-template-columns: 1fr; gap:24px;">

        {{-- Bulletins de paie --}}
        <x-filament::section heading="Mes bulletins de paie">
            {{ $this->table }}
        </x-filament::section>

        {{-- Sidebar en 3 colonnes sous les bulletins --}}
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:24px;">

            {{-- Soldes congés --}}
            <x-filament::section heading="Soldes de congés {{ now()->year }}">
                @forelse($balances as $b)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--fi-gray-100);">
                    <span style="font-size:14px; color:var(--fi-gray-600);">{{ $b['type'] }}</span>
                    <span>
                        <strong style="color:#0da8b1;">{{ $b['remaining'] }} j</strong>
                        <span style="font-size:12px; color:var(--fi-gray-400);"> / {{ $b['total'] }}</span>
                    </span>
                </div>
                @empty
                <p style="font-size:13px; color:var(--fi-gray-400); text-align:center; padding:12px 0;">Aucun solde</p>
                @endforelse
            </x-filament::section>

            {{-- Demandes en attente --}}
            <x-filament::section heading="Demandes en attente ({{ count($pending) }})">
                @forelse($pending as $p)
                <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:10px 12px; margin-bottom:8px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:13px; font-weight:600; color:#92400e;">{{ $p['type'] }}</span>
                        <x-filament::badge color="warning">{{ $p['jours'] }} j</x-filament::badge>
                    </div>
                    <div style="font-size:12px; color:#b45309; margin-top:4px;">{{ $p['debut'] }} → {{ $p['fin'] }}</div>
                </div>
                @empty
                <p style="font-size:13px; color:var(--fi-gray-400); text-align:center; padding:12px 0;">Aucune demande en attente</p>
                @endforelse
            </x-filament::section>

            {{-- Mon contrat --}}
            @if($employee)
            <x-filament::section heading="Mon contrat">
                @foreach(array_filter([
                    'Type'       => $employee->contract_type,
                    'Embauche'   => $employee->hire_date?->format('d/m/Y'),
                    'CIN'        => $employee->cin,
                    'N° CNSS'    => $employee->cnss_number,
                ]) as $label => $value)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--fi-gray-100);">
                    <span style="font-size:13px; color:var(--fi-gray-500);">{{ $label }}</span>
                    @if($label === 'Type')
                        <x-filament::badge color="primary">{{ $value }}</x-filament::badge>
                    @else
                        <span style="font-size:13px; font-weight:500; color:var(--fi-gray-700);">{{ $value }}</span>
                    @endif
                </div>
                @endforeach
            </x-filament::section>
            @endif

        </div>
    </div>

</x-filament-panels::page>
