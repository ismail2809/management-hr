<?php

namespace App\Filament\App\Pages;

use App\Models\EcoleSettings as EcoleSettingsModel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EcoleSettings extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    protected string $view = 'filament.app.pages.ecole-settings';

    protected static ?string $navigationLabel = 'Paramètres École';

    protected static ?string $title = 'Paramètres de l\'École';

    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public function mount(): void
    {
        $settings = EcoleSettingsModel::get();
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Informations de l\'établissement')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nom_ecole')
                                ->label('Nom de l\'école')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('code_massare')
                                ->label('Code MASSAR')
                                ->maxLength(50)
                                ->helperText('Code de l\'établissement dans le système MASSAR'),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('cnss')
                                ->label('N° CNSS')
                                ->maxLength(50),

                            TextInput::make('patente')
                                ->label('Patente')
                                ->maxLength(50),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('rc')
                                ->label('RC (Registre de Commerce)')
                                ->maxLength(50),

                            TextInput::make('if_number')
                                ->label('IF (Identifiant Fiscal)')
                                ->maxLength(50),
                        ]),
                    ]),

                Section::make('Coordonnées')
                    ->schema([
                        Textarea::make('adresse')
                            ->label('Adresse')
                            ->rows(2)
                            ->columnSpanFull(),

                        Grid::make(3)->schema([
                            TextInput::make('code_postal')
                                ->label('Code postal')
                                ->maxLength(10),

                            TextInput::make('ville')
                                ->label('Ville')
                                ->maxLength(100),

                            TextInput::make('pays')
                                ->label('Pays')
                                ->default('Maroc')
                                ->maxLength(100),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('telephone')
                                ->label('Téléphone')
                                ->tel()
                                ->maxLength(20),

                            TextInput::make('fax')
                                ->label('Fax')
                                ->maxLength(20),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255),

                            TextInput::make('site_web')
                                ->label('Site web')
                                ->url()
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Logo et Cachet')
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('logo')
                                ->label('Logo de l\'école')
                                ->image()
                                ->directory('ecole')
                                ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
                                ->maxSize(2048)
                                ->helperText('Format recommandé : PNG transparent, 300x100px'),

                            FileUpload::make('cachet')
                                ->label('Cachet officiel')
                                ->image()
                                ->directory('ecole')
                                ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
                                ->maxSize(2048)
                                ->helperText('Format recommandé : PNG transparent, 200x200px'),
                        ]),

                        Grid::make(2)->schema([
                            Toggle::make('afficher_logo_pdf')
                                ->label('Afficher le logo sur les documents')
                                ->default(true),

                            Toggle::make('afficher_cachet_pdf')
                                ->label('Afficher le cachet sur les documents')
                                ->default(true),
                        ]),
                    ]),

                Section::make('Textes des documents')
                    ->schema([
                        Textarea::make('entete_document')
                            ->label('En-tête des documents')
                            ->rows(3)
                            ->placeholder('Texte affiché en haut des documents générés'),

                        Textarea::make('pied_document')
                            ->label('Pied de page des documents')
                            ->rows(3)
                            ->placeholder('Mentions légales, conditions, etc.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = EcoleSettingsModel::get();
        $settings->update($data);

        Notification::make()
            ->success()
            ->title('Paramètres sauvegardés')
            ->body('Les paramètres de l\'école ont été mis à jour avec succès.')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Enregistrer')
                ->submit('save'),
        ];
    }
}
