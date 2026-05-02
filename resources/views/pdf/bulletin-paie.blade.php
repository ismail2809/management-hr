<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }

    .page { padding: 20px 28px; }

    /* En-tête */
    .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1d4ed8; padding-bottom: 12px; margin-bottom: 16px; }
    .company-name { font-size: 16px; font-weight: bold; color: #1d4ed8; }
    .company-info { font-size: 10px; color: #555; margin-top: 4px; line-height: 1.5; }
    .bulletin-title { text-align: right; }
    .bulletin-title h1 { font-size: 14px; font-weight: bold; color: #1d4ed8; }
    .bulletin-title .periode { font-size: 13px; font-weight: bold; color: #333; margin-top: 2px; }

    /* Infos employé */
    .section { margin-bottom: 14px; }
    .section-title { background: #1d4ed8; color: white; padding: 4px 8px; font-size: 10px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; }
    .info-grid { display: flex; flex-wrap: wrap; gap: 6px 0; }
    .info-row { width: 50%; display: flex; font-size: 10px; }
    .info-label { color: #666; width: 140px; flex-shrink: 0; }
    .info-value { font-weight: 600; color: #222; }

    /* Table des éléments */
    table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 14px; }
    thead tr { background: #e0e7ff; }
    thead th { padding: 5px 8px; text-align: left; font-weight: bold; font-size: 9.5px; color: #1e3a8a; text-transform: uppercase; border-bottom: 1px solid #c7d2fe; }
    thead th.right { text-align: right; }
    tbody tr { border-bottom: 1px solid #f0f0f0; }
    tbody tr:nth-child(even) { background: #f8f9ff; }
    tbody td { padding: 4px 8px; }
    tbody td.right { text-align: right; }
    tbody td.label { color: #444; }
    .subtotal-row td { background: #f0f4ff; font-weight: 600; border-top: 1px solid #c7d2fe; }

    /* Récap net */
    .net-box { background: #1d4ed8; color: white; padding: 12px 16px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; }
    .net-label { font-size: 13px; font-weight: bold; letter-spacing: 0.3px; }
    .net-amount { font-size: 20px; font-weight: bold; }

    /* Cotisations patronales */
    .patronal-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 8px 12px; font-size: 10px; }
    .patronal-row { display: flex; justify-content: space-between; padding: 2px 0; }
    .patronal-label { color: #555; }
    .patronal-value { font-weight: 600; }

    /* Footer */
    .footer { border-top: 1px solid #e5e7eb; padding-top: 8px; margin-top: 10px; display: flex; justify-content: space-between; font-size: 9px; color: #888; }
    .signature-zone { text-align: right; font-size: 10px; color: #333; }
    .signature-line { border-top: 1px solid #555; margin-top: 30px; width: 160px; }
</style>
</head>
<body>
<div class="page">

    {{-- EN-TÊTE --}}
    <div class="header">
        <div>
            <div class="company-name">{{ $company->name }}</div>
            <div class="company-info">
                ICE : {{ $company->ice ?? '—' }} | RC : {{ $company->rc ?? '—' }}<br>
                CNSS : {{ $company->cnss_affiliation ?? '—' }}<br>
                {{ $company->city ?? '' }} | {{ $company->email ?? '' }}
            </div>
        </div>
        <div class="bulletin-title">
            <h1>BULLETIN DE PAIE</h1>
            <div class="periode">{{ $mois }} {{ $payroll->year }}</div>
        </div>
    </div>

    {{-- EMPLOYÉ --}}
    <div class="section">
        <div class="section-title">Informations Employé</div>
        <div class="info-grid">
            <div class="info-row"><span class="info-label">Matricule</span><span class="info-value">{{ $employee->matricule }}</span></div>
            <div class="info-row"><span class="info-label">CIN</span><span class="info-value">{{ $employee->cin }}</span></div>
            <div class="info-row"><span class="info-label">Nom complet</span><span class="info-value">{{ $employee->full_name }}</span></div>
            <div class="info-row"><span class="info-label">N° CNSS</span><span class="info-value">{{ $employee->cnss_number ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Poste</span><span class="info-value">{{ $employee->position?->title ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Département</span><span class="info-value">{{ $employee->department?->name ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Type de contrat</span><span class="info-value">{{ $employee->contract_type }}</span></div>
            <div class="info-row"><span class="info-label">Date d'embauche</span><span class="info-value">{{ $employee->hire_date?->format('d/m/Y') ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Situation familiale</span><span class="info-value">{{ ucfirst($employee->marital_status) }} — {{ $employee->number_of_children }} enfant(s)</span></div>
            <div class="info-row"><span class="info-label">RIB</span><span class="info-value">{{ $employee->rib ?? '—' }}</span></div>
        </div>
    </div>

    {{-- ÉLÉMENTS DE PAIE --}}
    <div class="section">
        <div class="section-title">Éléments de Rémunération</div>
        <table>
            <thead>
                <tr>
                    <th style="width:55%">Libellé</th>
                    <th class="right">Gains (MAD)</th>
                    <th class="right">Retenues (MAD)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="label">Salaire de base</td>
                    <td class="right">{{ number_format($payroll->salaire_brut, 2, ',', ' ') }}</td>
                    <td class="right">—</td>
                </tr>
                @foreach ($components as $comp)
                    <tr>
                        <td class="label">{{ $comp->label }} <span style="color:#888;font-size:9px">({{ $comp->type }})</span></td>
                        @if ($comp->type === 'retenue')
                            <td class="right">—</td>
                            <td class="right">{{ number_format($comp->amount, 2, ',', ' ') }}</td>
                        @else
                            <td class="right">{{ number_format($comp->amount, 2, ',', ' ') }}</td>
                            <td class="right">—</td>
                        @endif
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td>Salaire brut imposable</td>
                    <td class="right">{{ number_format($payroll->salaire_brut, 2, ',', ' ') }}</td>
                    <td class="right">—</td>
                </tr>
                <tr>
                    <td class="label">CNSS salarié ({{ number_format(4.48, 2) }}% — plafond 6 000)</td>
                    <td class="right">—</td>
                    <td class="right">{{ number_format($payroll->total_cnss_employee, 2, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td class="label">AMO salarié (2.26%)</td>
                    <td class="right">—</td>
                    <td class="right">{{ number_format($payroll->amo_employee, 2, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td class="label">Impôt sur le Revenu (IR)</td>
                    <td class="right">—</td>
                    <td class="right">{{ number_format($payroll->ir, 2, ',', ' ') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- NET À PAYER --}}
    <div class="net-box">
        <div class="net-label">NET À PAYER</div>
        <div class="net-amount">{{ number_format($payroll->salaire_net, 2, ',', ' ') }} MAD</div>
    </div>

    {{-- CHARGES PATRONALES --}}
    <div class="section">
        <div class="section-title">Charges Patronales (à titre indicatif)</div>
        <div class="patronal-box">
            <div class="patronal-row">
                <span class="patronal-label">CNSS patronal (10.77%)</span>
                <span class="patronal-value">{{ number_format($payroll->total_cnss_employer, 2, ',', ' ') }} MAD</span>
            </div>
            <div class="patronal-row">
                <span class="patronal-label">AMO patronal (4.11%)</span>
                <span class="patronal-value">{{ number_format($payroll->amo_employer, 2, ',', ' ') }} MAD</span>
            </div>
            <div class="patronal-row" style="font-weight:600; border-top:1px solid #e5e7eb; margin-top:4px; padding-top:4px;">
                <span class="patronal-label">Coût total employeur</span>
                <span class="patronal-value">{{ number_format($payroll->salaire_brut + $payroll->total_cnss_employer + $payroll->amo_employer, 2, ',', ' ') }} MAD</span>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div>
            Document généré le {{ now()->format('d/m/Y à H:i') }}<br>
            Période : {{ $mois }} {{ $payroll->year }}
        </div>
        <div class="signature-zone">
            Signature employeur<br>
            <div class="signature-line"></div>
        </div>
    </div>

</div>
</body>
</html>
