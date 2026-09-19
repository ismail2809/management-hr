<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentPdfController extends Controller
{
    public function download(DocumentRequest $documentRequest)
    {
        return $this->render($documentRequest, download: true);
    }

    public function downloadFinal(DocumentRequest $documentRequest)
    {
        $user = auth()->user();
        abort_if(
            ! $user->hasRole('super-admin') && $user->ecole_setting_id !== $documentRequest->ecole_setting_id,
            403
        );
        abort_unless(filled($documentRequest->fichier_final), 404);

        $documentRequest->increment('nb_telechargements');

        return redirect()->away(asset('storage/' . $documentRequest->fichier_final));
    }

    public function preview(DocumentRequest $documentRequest)
    {
        return $this->render($documentRequest, download: false);
    }

    private function render(DocumentRequest $documentRequest, bool $download)
    {
        $user = auth()->user();
        abort_if(
            ! $user->hasRole('super-admin') && $user->ecole_setting_id !== $documentRequest->ecole_setting_id,
            403
        );

        $documentRequest->load(['employee.profession', 'ecoleSettings']);

        if ($download) {
            $documentRequest->update([
                'status'       => 'approuvé',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);
        }

        $employee = $documentRequest->employee;
        $company  = $documentRequest->ecoleSettings;
        $date     = now()->locale('fr')->isoFormat('D MMMM YYYY');

        $allowedTypes = array_keys(DocumentRequest::$documentTypes + DocumentRequest::$autreTypes);
        abort_unless(in_array($documentRequest->type, $allowedTypes, true), 400);

        $view = 'pdf.documents.' . $documentRequest->type;
        if (! view()->exists($view)) {
            $view = 'pdf.documents.generic';
        }

        $pdf = Pdf::loadView($view, compact('documentRequest', 'employee', 'company', 'date'))
            ->setPaper('a4', 'portrait');

        $filename = $documentRequest->type . '-' . $employee->matricule . '-' . now()->format('Y-m-d') . '.pdf';

        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
}
