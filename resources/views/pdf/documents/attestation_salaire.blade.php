<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
    .page { padding: 40px 50px; }
    .header { text-align: center; border-bottom: 2px solid #0da8b1; padding-bottom: 16px; margin-bottom: 30px; }
    .company-name { font-size: 18px; font-weight: bold; color: #0da8b1; }
    .company-info { font-size: 10px; color: #555; margin-top: 4px; line-height: 1.6; }
    .doc-title { text-align: center; margin: 30px 0; }
    .doc-title h1 { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #222; border-bottom: 1px solid #333; display: inline-block; padding-bottom: 6px; }
    .body-text { line-height: 1.9; font-size: 12px; text-align: justify; margin-bottom: 20px; }
    .body-text .highlight { font-weight: bold; color: #0a1628; }
    .info-box { background: #f0fdfe; border-left: 4px solid #0da8b1; padding: 12px 16px; margin: 20px 0; font-size: 11px; }
    .info-box .row { display: flex; margin-bottom: 6px; }
    .info-box .label { width: 200px; color: #555; flex-shrink: 0; }
    .info-box .value { font-weight: bold; }
    .salary-box { background: #0da8b1; color: white; text-align: center; padding: 16px; margin: 20px 0; border-radius: 4px; }
    .salary-box .amount { font-size: 22px; font-weight: bold; }
    .salary-box .label { font-size: 11px; margin-top: 4px; opacity: 0.85; }
    .footer-note { font-size: 10px; color: #777; margin-top: 30px; font-style: italic; }
    .signature-block { margin-top: 50px; display: flex; justify-content: space-between; }
    .sig-right { width: 45%; text-align: center; }
    .sig-right .sig-title { font-weight: bold; margin-bottom: 60px; }
    .sig-right .sig-name { border-top: 1px solid #333; padding-top: 6px; font-size: 10px; color: #555; }
    .date-lieu { margin-top: 40px; font-size: 11px; }
    .stamp-area { border: 1px dashed #aaa; width: 120px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #aaa; margin: 0 auto 8px; }
</style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div class="company-info">
            {{ $company->address ?? '' }}{{ $company->city ? ', ' . $company->city : '' }}<br>
            @if($company->phone) Tél : {{ $company->phone }} — @endif
            @if($company->email) Email : {{ $company->email }} @endif<br>
            @if($company->rc) RC : {{ $company->rc }} — @endif
            @if($company->ice) ICE : {{ $company->ice }} @endif
        </div>
    </div>

    <div class="doc-title">
        <h1>Attestation de Salaire</h1>
    </div>

    <div class="body-text">
        Je soussigné(e), représentant légal de la société <span class="highlight">{{ $company->name }}</span>,
        atteste par la présente que :
    </div>

    <div class="info-box">
        <div class="row"><span class="label">Nom et prénom :</span><span class="value">{{ $employee->full_name }}</span></div>
        <div class="row"><span class="label">CIN :</span><span class="value">{{ $employee->cin ?? '—' }}</span></div>
        <div class="row"><span class="label">N° CNSS :</span><span class="value">{{ $employee->cnss_number ?? '—' }}</span></div>
        <div class="row"><span class="label">Poste occupé :</span><span class="value">{{ $employee->profession?->name ?? '—' }}</span></div>
        <div class="row"><span class="label">Type de contrat :</span><span class="value">{{ $employee->contract_type }}</span></div>
        @if($employee->hire_date)
        <div class="row"><span class="label">Date d'embauche :</span><span class="value">{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</span></div>
        @endif
    </div>

    @php
        $lastPayroll = \App\Models\Payroll::where('employee_id', $employee->id)
            ->where('status', '!=', 'brouillon')
            ->orderByDesc('year')->orderByDesc('month')
            ->first();
    @endphp

    @if($lastPayroll)
    <div class="salary-box">
        <div class="amount">{{ number_format($lastPayroll->salaire_net, 2, ',', ' ') }} MAD</div>
        <div class="label">Salaire net mensuel ({{ ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'][$lastPayroll->month - 1] }} {{ $lastPayroll->year }})</div>
    </div>

    <div class="body-text">
        Le salaire brut mensuel de l'intéressé(e) s'élève à
        <span class="highlight">{{ number_format($lastPayroll->salaire_brut, 2, ',', ' ') }} MAD</span>
        pour un salaire net de
        <span class="highlight">{{ number_format($lastPayroll->salaire_net, 2, ',', ' ') }} MAD</span>.
    </div>
    @else
    <div class="body-text">
        Le salaire mensuel de l'intéressé(e) est conforme aux dispositions de son contrat de travail.
    </div>
    @endif

    <div class="body-text">
        Cette attestation est délivrée à l'intéressé(e) sur sa demande pour servir et valoir ce que de droit.
    </div>

    @if($documentRequest->reason)
    <div class="body-text"><em>Motif : {{ $documentRequest->reason }}</em></div>
    @endif

    <div class="date-lieu">
        Fait à {{ $company->city ?? '___________' }}, le {{ $date }}
    </div>

    <div class="signature-block">
        <div></div>
        <div class="sig-right">
            <div class="stamp-area">Cachet &amp; Signature</div>
            <div class="sig-title">La Direction</div>
            <div class="sig-name">{{ $company->name }}</div>
        </div>
    </div>

    <div class="footer-note">
        Document généré le {{ $date }} — {{ $company->name }} — Réf. DRH-AS-{{ $employee->matricule }}-{{ now()->format('Ymd') }}
    </div>
</div>
</body>
</html>
