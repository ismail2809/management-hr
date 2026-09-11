@props(['url'])
@php
    $ecole = \App\Models\EcoleSettings::first();
    $logoPath = $ecole?->logo ? storage_path('app/public/' . $ecole->logo) : null;
    $logoUrl  = $logoPath && file_exists($logoPath)
        ? asset('storage/' . $ecole->logo)
        : null;
    $nomEcole = $ecole?->nom_ecole ?? config('app.name');
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
    @if ($logoUrl)
        <img src="{{ $logoUrl }}" alt="{{ $nomEcole }}" style="max-height: 70px; max-width: 200px; object-fit: contain;">
    @else
        <span style="font-size: 20px; font-weight: bold; color: #1e40af;">{{ $nomEcole }}</span>
    @endif
</a>
</td>
</tr>
