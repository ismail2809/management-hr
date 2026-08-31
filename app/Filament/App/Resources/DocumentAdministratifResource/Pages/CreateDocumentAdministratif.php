<?php

namespace App\Filament\App\Resources\DocumentAdministratifResource\Pages;

use App\Filament\App\Resources\DocumentAdministratifResource;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Employee;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;

class CreateDocumentAdministratif extends CreateRecord
{
    use HasWizard;

    protected static string $resource = DocumentAdministratifResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['categorie'] = 'document';

        $user = \Filament\Facades\Filament::auth()->user();
        if (empty($data['company_id'])) {
            $data['company_id'] = $user?->company_id;
        }

        return $data;
    }

    protected function getSteps(): array
    {
        $isEmployee = auth()->user()?->hasRole('employee');

        return [
            Step::make('Demandeur')
                ->description('Sélectionnez l\'employé concerné.')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    DocumentAdministratifResource::companyField(),

                    Section::make('Employé(e)')
                        ->icon('heroicon-o-user')
                        ->compact()
                        ->schema([
                            Select::make('employee_id')
                                ->label('Employé(e)')
                                ->relationship('employee', 'first_name')
                                ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                                ->searchable()
                                ->preload()
                                ->default(fn () => auth()->user()?->employee_id)
                                ->disabled($isEmployee)
                                ->dehydrated()
                                ->required()
                                ->columnSpanFull(),
                        ]),
                ]),

            Step::make('Document administratif')
                ->description('Choisissez le type et les détails.')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Section::make('Type de document')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Select::make('type')
                                ->label('Type de document')
                                ->options(fn () => DocumentType::where('active', true)->where('categorie', 'document')->orderBy('sort_order')->pluck('name', 'code')->toArray() ?: DocumentRequest::$documentTypes)
                                ->required(),

                            Radio::make('format')
                                ->label('Format souhaité')
                                ->options(['digital' => 'Version digitale (PDF)', 'papier' => 'Version papier'])
                                ->default('digital')
                                ->inline(),
                        ]),

                    Section::make('Détails')->schema([
                        Textarea::make('description')
                            ->label('Description / détails')
                            ->rows(3)
                            ->nullable(),

                        Textarea::make('reason')
                            ->label('Remarques complémentaires')
                            ->rows(2)
                            ->nullable(),

                        Select::make('status')
                            ->label('Statut')
                            ->options(['en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé'])
                            ->default('en_attente')
                            ->disabled($isEmployee)
                            ->dehydrated()
                            ->required(),

                        FileUpload::make('fichier_final')
                            ->label('Fichier final (uploadé par l\'admin)')
                            ->disk('public')
                            ->directory('document-requests/finals')
                            ->acceptedFileTypes([
                                'application/pdf', 'image/jpeg', 'image/png', 'image/webp',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(10240)
                            ->nullable()
                            ->hidden($isEmployee),
                    ]),
                ]),
        ];
    }
}
