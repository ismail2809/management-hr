<?php

namespace App\Filament\Admin\Resources\EmployeeResource\Pages;

use App\Filament\Admin\Resources\EmployeeResource;
use App\Models\EcoleSettings;
use App\Services\EmployeeImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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
                ->form(array_filter([
                    auth()->user()->ecole_setting_id === null
                        ? Select::make('ecole_setting_id')
                            ->label('École')
                            ->options(EcoleSettings::pluck('nom_ecole', 'id'))
                            ->required()
                            ->searchable()
                        : null,
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
                ]))
                ->action(function (array $data): void {
                    $companyId = auth()->user()->ecole_setting_id ?? $data['ecole_setting_id'] ?? null;

                    if (! $companyId) {
                        Notification::make()
                            ->title('Erreur')
                            ->body('Aucune company sélectionnée.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $path = Storage::disk('local')->path($data['file']);

                    $service = new EmployeeImportService();
                    $result  = $service->import($path, $companyId);

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
