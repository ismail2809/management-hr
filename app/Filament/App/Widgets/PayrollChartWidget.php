<?php

namespace App\Filament\App\Widgets;

use App\Models\Payroll;
use Filament\Widgets\ChartWidget;

class PayrollChartWidget extends ChartWidget
{
    protected ?string $heading = 'Masse salariale brute — 6 derniers mois';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    protected function getData(): array
    {
        $labels = [];
        $data   = [];

        $mois = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
                 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

        for ($i = 5; $i >= 0; $i--) {
            $date   = now()->subMonths($i);
            $month  = (int) $date->format('m');
            $year   = (int) $date->format('Y');

            $total = Payroll::where('month', $month)
                ->where('year', $year)
                ->sum('salaire_brut');

            $labels[] = $mois[$month] . ' ' . $year;
            $data[]   = round($total, 2);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Salaire brut (MAD)',
                    'data'            => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor'     => 'rgb(59, 130, 246)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
