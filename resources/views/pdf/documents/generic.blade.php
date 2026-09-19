<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  @page { size: A4 portrait; margin: 0; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }
  .corner-top-right { position: fixed; top: 0; right: 0; width: 0; height: 0; border-style: solid; border-width: 0 130px 130px 0; border-color: transparent #1c3f6e transparent transparent; }
  .corner-bottom-left { position: fixed; bottom: 0; left: 0; width: 0; height: 0; border-style: solid; border-width: 0 0 110px 165px; border-color: transparent transparent #2a9d8f transparent; }
  .doc-footer { position: fixed; bottom: 10mm; left: 18mm; right: 18mm; border-top: 1px solid #ccc; padding-top: 7px; font-size: 9px; color: #333; line-height: 1.7; }
  .footer-left { float: left; }
  .footer-right { float: right; text-align: right; }
  .footer-clear { clear: both; }
  .content { padding: 20mm 20mm 36mm 20mm; position: relative; }
  .header-left { float: left; }
  .header-right { float: right; text-align: right; font-size: 12px; line-height: 1.8; padding-top: 6px; }
  .header-clear { clear: both; margin-bottom: 30px; }
  .brand-logo img { height: 70px; width: auto; display: block; }
  .title-wrap { text-align: center; margin: 10px 0 38px 0; }
  .title-box { display: inline-block; border: 2.5px solid #1a1a1a; padding: 14px 50px; font-weight: bold; font-size: 16px; letter-spacing: 3px; line-height: 1.6; text-align: center; }
  .body-text { font-size: 13px; line-height: 2.1; text-align: justify; }
  .body-text p { margin-bottom: 22px; text-indent: 40px; }
  .signature { text-align: right; margin-top: 55px; font-weight: bold; font-size: 13px; }
</style>
</head>
<body>

<div class="corner-top-right"></div>
<div class="corner-bottom-left"></div>

<div class="doc-footer">
  <div class="footer-left">
    @if($company->patente)PATENTE : {{ $company->patente }}@if($company->cnss_affiliation) &nbsp;–&nbsp; CNSS : {{ $company->cnss_affiliation }}@endif<br>@endif
    @if($company->rc)RC : {{ $company->rc }}@endif@if($company->ice) &nbsp;–&nbsp; IF : {{ $company->ice }}@endif
  </div>
  <div class="footer-right">
    @if($company->email)✉ {{ $company->email }}<br>@endif
    @if($company->phone)☏ {{ $company->phone }}<br>@endif
    @if($company->city)⚲ {{ $company->city }}@endif
  </div>
  <div class="footer-clear"></div>
</div>

<div class="content">

  @php
    $civilite   = match($employee->gender ?? '') { 'female','F','f' => 'Mme', default => 'M.' };
    $nomComplet = strtoupper($employee->last_name) . ' ' . $employee->first_name;
    $ref        = $documentRequest->id . '/DRH/' . now()->format('Y') . '.' . now()->format('m');
    $dateEd     = now()->format('d / m / Y');
    $typeLabel  = $documentRequest->type ?? 'Document';
  @endphp

  <div class="header-left">
    <div class="brand-logo">
      <img src="{{ public_path('logo.png') }}" alt="{{ $company->name }}">
    </div>
  </div>
  <div class="header-right">
    Réf : {{ $ref }}<br>
    {{ $company->city ?? '' }}, le {{ $dateEd }}
  </div>
  <div class="header-clear"></div>

  <div class="title-wrap">
    <div class="title-box">{{ strtoupper(str_replace('_', ' ', $typeLabel)) }}</div>
  </div>

  <div class="body-text">
    <p>
      Je soussigné(e), représentant légal de <strong>{{ $company->name }}</strong>@if($company->city), sise à {{ $company->city }}@endif,
      atteste par la présente que
      <strong>{{ $civilite }} {{ $nomComplet }}</strong>,
      titulaire de la CIN <strong>{{ $employee->cin ?? '—' }}</strong>,
      est membre du personnel de notre établissement.
    </p>
    @if($documentRequest->description)
    <p>{{ $documentRequest->description }}</p>
    @endif
    <p>
      En foi de quoi, la présente est délivrée, sur demande de l'intéressé(e) pour servir et valoir ce que de droit.
    </p>
  </div>

  <div class="signature">Signée :</div>

</div>

</body>
</html>
