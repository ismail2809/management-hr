<?php

namespace App\Filament\App\Resources\EmployeeResource\Pages;

use App\Filament\App\Resources\EmployeeResource;
use App\Services\EmployeeImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importEmployees')
                ->label('Importer Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->form([
                    FileUpload::make('file')
                        ->label('Fichier Excel (XLS / XLSX)')
                        ->disk('local')
                        ->directory('imports/employees')
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/octet-stream',
                        ])
                        ->maxSize(10240)
                        ->required()
                        ->helperText('Colonnes reconnues : Matricule, Nom, Prénom, CIN, CNSS, Sexe, Date naissance, Date recrutement, Diplôme, Nationalité, Adresse'),
                ])
                ->action(function (array $data): void {
                    $path = Storage::disk('local')->path($data['file']);

                    $service = new EmployeeImportService();
                    $result  = $service->import($path, auth()->user()->company_id);

                    // Supprimer le fichier temporaire
                    Storage::disk('local')->delete($data['file']);

                    if ($result['imported'] > 0) {
                        Notification::make()
                            ->title("Import terminé")
                            ->body("{$result['imported']} employé(s) importé(s), {$result['skipped']} ignoré(s).")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title("Aucun employé importé")
                            ->body("Vérifiez le format du fichier.")
                            ->warning()
                            ->send();
                    }

                    if (! empty($result['errors'])) {
                        Notification::make()
                            ->title('Erreurs lors de l\'import')
                            ->body(implode("\n", array_slice($result['errors'], 0, 5)))
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                })
                ->modalHeading('Importer des employés')
                ->modalDescription('Importez vos employés depuis un fichier Excel. Les employés existants (même CIN) seront mis à jour.')
                ->modalSubmitActionLabel('Importer')
                ->slideOver(),

            CreateAction::make(),
        ];
    }
}
