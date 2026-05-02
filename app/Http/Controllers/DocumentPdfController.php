<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentPdfController extends Controller
{
    public function download(DocumentRequest $documentRequest)
    {
        abort_if(auth()->user()->company_id !== $documentRequest->company_id, 403);

        $documentRequest->load(['employee.position', 'employee.department', 'company']);

        $documentRequest->update([
            'status'       => 'traité',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

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

        return $pdf->download($filename);
    }
}
