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
    .highlight { font-weight: bold; color: #0a1628; }
    .info-box { background: #f0fdfe; border-left: 4px solid #0da8b1; padding: 12px 16px; margin: 20px 0; font-size: 11px; }
    .info-box .row { display: flex; margin-bottom: 6px; }
    .info-box .label { width: 220px; color: #555; flex-shrink: 0; }
    .info-box .value { font-weight: bold; }
    .cnss-badge { background: #1e40af; color: white; text-align: center; padding: 10px; margin: 15px 0; font-size: 13px; font-weight: bold; letter-spacing: 1px; }
    .footer-note { font-size: 10px; color: #777; margin-top: 30px; font-style: italic; }
    .signature-block { margin-top: 50px; text-align: right; }
    .stamp-area { border: 1px dashed #aaa; width: 120px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #aaa; display: inline-block; text-align: center; padding-top: 30px; margin-bottom: 8px; }
    .date-lieu { margin-top: 40px; font-size: 11px; }
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
            @if($company->cnss_affiliation) N° Affiliation CNSS : {{ $company->cnss_affiliation }} @endif
        </div>
    </div>

    <div class="doc-title">
        <h1>Attestation d'Affiliation CNSS</h1>
    </div>

    <div class="body-text">
        Je soussigné(e), représentant légal de la société <span class="highlight">{{ $company->name }}</span>,
        affiliée à la Caisse Nationale de Sécurité Sociale (CNSS)
        @if($company->cnss_affiliation)
        sous le numéro <span class="highlight">{{ $company->cnss_affiliation }}</span>,
        @endif
        atteste par la présente que :
    </div>

    <div class="info-box">
        <div class="row"><span class="label">Nom et prénom :</span><span class="value">{{ $employee->full_name }}</span></div>
        <div class="row"><span class="label">CIN :</span><span class="value">{{ $employee->cin ?? '—' }}</span></div>
        <div class="row"><span class="label">N° CNSS salarié :</span><span class="value">{{ $employee->cnss_number ?? '—' }}</span></div>
        <div class="row"><span class="label">Poste :</span><span class="value">{{ $employee->position->title ?? '—' }}</span></div>
        @if($employee->hire_date)
        <div class="row"><span class="label">Date d'affiliation :</span><span class="value">{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</span></div>
        @endif
        <div class="row"><span class="label">Statut :</span><span class="value">Affilié(e) et en règle</span></div>
    </div>

    <div class="body-text">
        L'intéressé(e) est régulièrement déclaré(e) à la CNSS par notre société depuis son entrée en service.
        Les cotisations patronales et salariales sont versées conformément à la réglementation en vigueur.
    </div>

    <div class="body-text">
        Cette attestation est délivrée sur demande de l'intéressé(e) pour servir et valoir ce que de droit.
    </div>

    @if($documentRequest->reason)
    <div class="body-text"><em>Motif : {{ $documentRequest->reason }}</em></div>
    @endif

    <div class="date-lieu">
        Fait à {{ $company->city ?? '___________' }}, le {{ $date }}
    </div>

    <div class="signature-block">
        <div class="stamp-area">Cachet &amp; Signature</div><br>
        <strong>La Direction — {{ $company->name }}</strong>
    </div>

    <div class="footer-note">
        Document généré le {{ $date }} — {{ $company->name }} — Réf. DRH-CNSS-{{ $employee->matricule }}-{{ now()->format('Ymd') }}
    </div>
</div>
</body>
</html>
