<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class PayrollBulkPdfController extends Controller
{
    public function download()
    {
        $ids   = request()->input('ids', []);
        $month = request()->input('month');
        $year  = request()->input('year');

        $query = Payroll::with(['employee.position', 'employee.department', 'components', 'company']);

        if ($ids) {
            $query->whereIn('id', $ids);
        } elseif ($month && $year) {
            $query->where('month', $month)->where('year', $year);
        } else {
            abort(400, 'Paramètres manquants.');
        }

        $payrolls = $query->get();
        abort_if($payrolls->isEmpty(), 404, 'Aucune fiche trouvée.');

        // Vérification multi-tenant
        $companyId = auth()->user()->company_id;
        $payrolls  = $payrolls->filter(fn ($p) => $p->company_id === $companyId);
        abort_if($payrolls->isEmpty(), 403);

        $moisLabels = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        // Un seul bulletin → PDF direct
        if ($payrolls->count() === 1) {
            $payroll = $payrolls->first();
            $pdf = Pdf::loadView('pdf.bulletin-paie', [
                'payroll'    => $payroll,
                'employee'   => $payroll->employee,
                'company'    => $payroll->company,
                'components' => $payroll->components,
                'mois'       => $moisLabels[$payroll->month] ?? $payroll->month,
            ])->setPaper('a4', 'portrait');

            $filename = 'bulletin-' . $payroll->employee->matricule
                . '-' . str_pad($payroll->month, 2, '0', STR_PAD_LEFT)
                . '-' . $payroll->year . '.pdf';

            return $pdf->download($filename);
        }

        // Plusieurs bulletins → ZIP
        $zipPath = storage_path('app/temp/bulletins-' . now()->timestamp . '.zip');
        @mkdir(dirname($zipPath), 0755, true);

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($payrolls as $payroll) {
            $pdf = Pdf::loadView('pdf.bulletin-paie', [
                'payroll'    => $payroll,
                'employee'   => $payroll->employee,
                'company'    => $payroll->company,
                'components' => $payroll->components,
                'mois'       => $moisLabels[$payroll->month] ?? $payroll->month,
            ])->setPaper('a4', 'portrait');

            $filename = 'bulletin-' . $payroll->employee->matricule
                . '-' . str_pad($payroll->month, 2, '0', STR_PAD_LEFT)
                . '-' . $payroll->year . '.pdf';

            $zip->addFromString($filename, $pdf->output());
        }

        $zip->close();

        $zipName = 'bulletins-paie-'
            . ($month ? str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . $year : now()->format('Y-m-d'))
            . '.zip';

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }
}
