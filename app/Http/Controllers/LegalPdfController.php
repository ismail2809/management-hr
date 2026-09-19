<?php

namespace App\Http\Controllers;

use App\Models\EcoleSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LegalPdfController extends Controller
{
    private function resolveLogoPath(EcoleSettings $settings): ?string
    {
        if (! $settings->logo) {
            return null;
        }

        // Try local (private) disk first, then public disk
        foreach (['local', 'public'] as $disk) {
            $path = Storage::disk($disk)->path($settings->logo);
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    public function mentionsLegales()
    {
        $settings = EcoleSettings::get();
        $logoPath = $this->resolveLogoPath($settings);

        $pdf = Pdf::loadView('pdf.mentions-legales', compact('settings', 'logoPath'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('mentions-legales.pdf');
    }

    public function politiqueConfidentialite()
    {
        $settings = EcoleSettings::get();
        $logoPath = $this->resolveLogoPath($settings);

        $pdf = Pdf::loadView('pdf.politique-confidentialite', compact('settings', 'logoPath'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('politique-confidentialite.pdf');
    }
}
