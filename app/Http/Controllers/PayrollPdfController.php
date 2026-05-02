<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollPdfController extends Controller
{
    public function download(Payroll $payroll)
    {
        // Vérifier que l'utilisateur appartient à la même company
        abort_if(auth()->user()->company_id !== $payroll->company_id, 403);

        $payroll->load(['employee.position', 'employee.department', 'components', 'company']);

        $mois = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        $pdf = Pdf::loadView('pdf.bulletin-paie', [
            'payroll'    => $payroll,
            'employee'   => $payroll->employee,
            'company'    => $payroll->company,
            'components' => $payroll->components,
            'mois'       => $mois[$payroll->month] ?? $payroll->month,
        ])->setPaper('a4', 'portrait');

        $filename = 'bulletin-' . $payroll->employee->matricule
            . '-' . str_pad($payroll->month, 2, '0', STR_PAD_LEFT)
            . '-' . $payroll->year . '.pdf';

        return $pdf->download($filename);
    }
}
