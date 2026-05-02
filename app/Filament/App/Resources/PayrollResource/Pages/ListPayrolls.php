<?php

namespace App\Filament\App\Resources\PayrollResource\Pages;

use App\Filament\App\Resources\PayrollResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_month')
                ->label('Bulletins du mois (ZIP)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->form([
                    Select::make('month')
                        ->label('Mois')
                        ->options([
                            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
                            4 => 'Avril',   5 => 'Mai',     6 => 'Juin',
                            7 => 'Juillet', 8 => 'Août',    9 => 'Septembre',
                            10 => 'Octobre',11 => 'Novembre',12 => 'Décembre',
                        ])
                        ->default(now()->month)
                        ->required(),
                    TextInput::make('year')
                        ->label('Année')
                        ->numeric()
                        ->default(now()->year)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $url = route('payrolls.bulk-pdf', [
                        'month' => $data['month'],
                        'year'  => $data['year'],
                    ]);
                    $this->redirect($url);
                }),

            CreateAction::make(),
        ];
    }
}
