<?php

namespace App\Filament\App\Pages;

use App\Models\Employee;
use App\Services\PayrollCalculator;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class GeneratePayroll extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Générer la paie';
    protected static \UnitEnum|string|null $navigationGroup = 'Paie';
    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.app.pages.generate-payroll';

    public ?array $data = [];
    public array $results = [];
    public bool $generated = false;

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->month,
            'year'  => now()->year,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Select::make('month')
                    ->label('Mois')
                    ->options([
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
                        4 => 'Avril',   5 => 'Mai',      6 => 'Juin',
                        7 => 'Juillet', 8 => 'Août',     9 => 'Septembre',
                        10 => 'Octobre',11 => 'Novembre',12 => 'Décembre',
                    ])
                    ->required(),

                TextInput::make('year')
                    ->label('Année')
                    ->numeric()
                    ->required(),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function generate(): void
    {
        $this->validate();

        $month      = (int) $this->data['month'];
        $year       = (int) $this->data['year'];
        $calculator = new PayrollCalculator();
        $employees  = Employee::with(['position', 'contracts'])->get();

        $this->results  = [];
        $this->generated = false;
        $errors = 0;

        foreach ($employees as $employee) {
            $salaire = (float) ($employee->position?->base_salary ?? 0);
            if ($salaire <= 0) {
                $this->results[] = [
                    'name'   => $employee->full_name,
                    'status' => 'ignoré',
                    'detail' => 'Aucun salaire de base défini',
                ];
                continue;
            }

            try {
                $payroll = $calculator->calculate($employee, $month, $year, $salaire);
                $this->results[] = [
                    'name'          => $employee->full_name,
                    'status'        => 'généré',
                    'detail'        => null,
                    'salaire_brut'  => $payroll->salaire_brut,
                    'cnss'          => $payroll->total_cnss_employee,
                    'amo'           => $payroll->amo_employee,
                    'ir'            => $payroll->ir,
                    'salaire_net'   => $payroll->salaire_net,
                ];
            } catch (\Throwable $e) {
                $errors++;
                $this->results[] = [
                    'name'   => $employee->full_name,
                    'status' => 'erreur',
                    'detail' => $e->getMessage(),
                ];
            }
        }

        $this->generated = true;
        $count = collect($this->results)->where('status', 'généré')->count();

        if ($errors === 0) {
            Notification::make()->title("{$count} fiches générées avec succès")->success()->send();
        } else {
            Notification::make()->title("{$count} fiches générées, {$errors} erreurs")->warning()->send();
        }
    }
}
