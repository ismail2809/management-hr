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
    .ref-line { text-align: right; font-size: 10px; color: #777; margin-bottom: 10px; }
    .doc-title { text-align: center; margin: 30px 0; }
    .doc-title h1 { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #222; border-bottom: 1px solid #333; display: inline-block; padding-bottom: 6px; }
    .body-text { line-height: 1.9; font-size: 12px; text-align: justify; margin-bottom: 20px; }
    .highlight { font-weight: bold; color: #0a1628; }
    .info-box { background: #f0fdfe; border-left: 4px solid #0da8b1; padding: 12px 16px; margin: 20px 0; font-size: 11px; }
    .info-box .row { display: flex; margin-bottom: 6px; }
    .info-box .label { width: 200px; color: #555; flex-shrink: 0; }
    .info-box .value { font-weight: bold; }
    .mission-box { border: 1px solid #0da8b1; padding: 14px 16px; margin: 20px 0; }
    .mission-box .title { font-weight: bold; color: #0da8b1; margin-bottom: 10px; font-size: 11px; text-transform: uppercase; }
    .footer-note { font-size: 10px; color: #777; margin-top: 30px; font-style: italic; }
    .signature-row { margin-top: 50px; display: flex; justify-content: space-between; }
    .sig-block { width: 45%; text-align: center; }
    .stamp-area { border: 1px dashed #aaa; width: 120px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #aaa; margin: 0 auto 8px; }
    .sig-title { font-weight: bold; font-size: 11px; margin-bottom: 4px; }
    .sig-name { font-size: 10px; color: #555; }
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
            @if($company->email) Email : {{ $company->email }} @endif
        </div>
    </div>

    <div class="ref-line">
        Réf. : OM-{{ $employee->matricule }}-{{ now()->format('Ymd') }} &nbsp;|&nbsp; Date : {{ $date }}
    </div>

    <div class="doc-title">
        <h1>Ordre de Mission</h1>
    </div>

    <div class="body-text">
        La Direction de <span class="highlight">{{ $company->name }}</span> donne mission à :
    </div>

    <div class="info-box">
        <div class="row"><span class="label">Nom et prénom :</span><span class="value">{{ $employee->full_name }}</span></div>
        <div class="row"><span class="label">CIN :</span><span class="value">{{ $employee->cin ?? '—' }}</span></div>
        <div class="row"><span class="label">Fonction :</span><span class="value">{{ $employee->position->title ?? '—' }}</span></div>
        <div class="row"><span class="label">Département :</span><span class="value">{{ $employee->department->name ?? '—' }}</span></div>
    </div>

    <div class="mission-box">
        <div class="title">Objet de la mission</div>
        <div>{{ $documentRequest->reason ?: 'Mission professionnelle selon les instructions de la hiérarchie.' }}</div>
    </div>

    <div class="body-text">
        L'intéressé(e) est autorisé(e) à se déplacer dans le cadre de ses fonctions.
        Les frais de déplacement engagés seront remboursés sur présentation des justificatifs, conformément à la politique interne de la société.
    </div>

    <div class="body-text">
        Toutes les autorités civiles et militaires sont priées de lui prêter aide et assistance si nécessaire.
    </div>

    <div class="date-lieu">
        Fait à {{ $company->city ?? '___________' }}, le {{ $date }}
    </div>

    <div class="signature-row">
        <div class="sig-block">
            <div class="sig-title">L'Intéressé(e)</div>
            <div style="height: 60px; border-bottom: 1px solid #333; margin-bottom: 6px;"></div>
            <div class="sig-name">{{ $employee->full_name }}</div>
        </div>
        <div class="sig-block">
            <div class="stamp-area">Cachet &amp; Signature</div>
            <div class="sig-title">La Direction</div>
            <div class="sig-name">{{ $company->name }}</div>
        </div>
    </div>

    <div class="footer-note">
        Document généré le {{ $date }} — {{ $company->name }} — Réf. DRH-OM-{{ $employee->matricule }}-{{ now()->format('Ymd') }}
    </div>
</div>
</body>
</html>
