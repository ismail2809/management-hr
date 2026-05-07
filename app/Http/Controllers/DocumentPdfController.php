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

    public function preview(DocumentRequest $documentRequest)
    {
        return $this->render($documentRequest, download: false);
    }

    private function render(DocumentRequest $documentRequest, bool $download)
    {
        $user = auth()->user();
        abort_if(
            ! $user->hasRole('super-admin') && $user->company_id !== $documentRequest->company_id,
            403
        );

        $documentRequest->load(['employee.position', 'employee.department', 'company']);

        if ($download) {
            $documentRequest->update([
                'status'       => 'traité',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);
        }

        $employee = $documentRequest->employee;
        $company  = $documentRequest->company;
        $date     = now()->locale('fr')->isoFormat('D MMMM YYYY');

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
