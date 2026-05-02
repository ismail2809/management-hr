<?php

namespace App\Filament\App\Resources\PayrollResource\Pages;

use App\Filament\App\Resources\PayrollResource;
use App\Models\Employee;
use App\Services\PayrollCalculator;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayroll extends EditRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('recalculate')
                ->label('Recalculer')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function () {
                    $payroll  = $this->record;
                    $employee = Employee::withoutGlobalScopes()->find($payroll->employee_id);

                    $components = $payroll->components->map(fn ($c) => [
                        'type'    => $c->type,
                        'label'   => $c->label,
                        'amount'  => (float) $c->amount,
                        'taxable' => $c->taxable,
                    ])->toArray();

                    app(PayrollCalculator::class)->calculate(
                        $employee,
                        $payroll->month,
                        $payroll->year,
                        (float) $payroll->salaire_brut,
                        $components
                    );

                    $this->record->refresh();
                    $this->fillForm();

                    Notification::make()->title('Paie recalculée')->success()->send();
                }),

            DeleteAction::make(),
        ];
    }
}
