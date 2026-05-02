<?php

namespace App\Filament\App\Resources\PayrollResource\Pages;

use App\Filament\App\Resources\PayrollResource;
use App\Models\Employee;
use App\Services\PayrollCalculator;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('calculer')
                ->label('Calculer')
                ->icon('heroicon-o-calculator')
                ->color('primary')
                ->action(function () {
                    $state       = $this->form->getState();
                    $employeeId  = $state['employee_id'] ?? null;
                    $salaireBrut = (float) ($state['salaire_brut'] ?? 0);
                    $month       = (int) ($state['month'] ?? now()->month);
                    $year        = (int) ($state['year'] ?? now()->year);

                    if (! $employeeId) {
                        Notification::make()->title('Sélectionnez un employé d\'abord')->warning()->send();
                        return;
                    }
                    if ($salaireBrut <= 0) {
                        Notification::make()->title('Saisissez un salaire brut valide')->warning()->send();
                        return;
                    }

                    $employee = Employee::withoutGlobalScopes()->find($employeeId);
                    $calculator = app(PayrollCalculator::class);

                    // Auto-fetch heures sup depuis Attendance
                    $overtimeHours = (float) ($state['overtime_hours'] ?? $calculator->fetchOvertimeHours($employeeId, $month, $year));

                    $isProrata         = (bool) ($state['is_prorata'] ?? false);
                    $totalWorkingDays  = $calculator->countWorkingDays($month, $year);
                    $workedDays        = $isProrata ? (int) ($state['worked_days'] ?? $totalWorkingDays) : null;

                    $components = collect($state['components'] ?? [])->map(fn ($c) => [
                        'type'    => $c['type'] ?? 'prime',
                        'label'   => $c['label'] ?? '',
                        'amount'  => (float) ($c['amount'] ?? 0),
                        'taxable' => (bool) ($c['taxable'] ?? true),
                    ])->toArray();

                    $values = $calculator->computeValues(
                        $employee, $salaireBrut, $components,
                        $overtimeHours, $isProrata, $workedDays, $totalWorkingDays
                    );

                    $this->form->fill(array_merge($state, [
                        'overtime_hours'      => $overtimeHours,
                        'overtime_amount'     => $values['overtimeAmount'],
                        'total_working_days'  => $totalWorkingDays,
                        'total_cnss_employee' => $values['cnssEmployee'],
                        'total_cnss_employer' => $values['cnssEmployer'],
                        'amo_employee'        => $values['amoEmployee'],
                        'amo_employer'        => $values['amoEmployer'],
                        'ir'                  => $values['ir'],
                        'salaire_net'         => $values['salaireNet'],
                    ]));

                    $msg = 'Net : ' . number_format($values['salaireNet'], 2, ',', ' ') . ' MAD';
                    if ($overtimeHours > 0) {
                        $msg .= ' (dont ' . number_format($values['overtimeAmount'], 2, ',', ' ') . ' MAD HS)';
                    }
                    if ($isProrata) {
                        $msg .= ' · Prorata ' . $workedDays . '/' . $totalWorkingDays . 'j';
                    }

                    Notification::make()->title($msg)->success()->send();
                }),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return array_merge($data, [
            'total_cnss_employee' => $data['total_cnss_employee'] ?? 0,
            'total_cnss_employer' => $data['total_cnss_employer'] ?? 0,
            'amo_employee'        => $data['amo_employee'] ?? 0,
            'amo_employer'        => $data['amo_employer'] ?? 0,
            'ir'                  => $data['ir'] ?? 0,
            'salaire_net'         => $data['salaire_net'] ?? 0,
            'overtime_hours'      => $data['overtime_hours'] ?? 0,
            'overtime_amount'     => $data['overtime_amount'] ?? 0,
        ]);
    }
}
