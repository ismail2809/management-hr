<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\ProfessionResource\Pages;
use App\Models\Profession;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfessionResource extends Resource
{
    use HasCompanyField;
    protected static ?string $model = Profession::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Professions';
    protected static ?string $modelLabel = 'Profession';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 20;

    public static function canViewAny(): bool { return ! auth()->user()?->hasRole('employee'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Profession')->schema([
                static::companyField(),
                TextInput::make('name')->label('Nom')->required()->maxLength(100),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Profession')->searchable()->sortable(),
                TextColumn::make('employees_count')->label('Employés')->counts('employees')->sortable(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProfessions::route('/'),
            'create' => Pages\CreateProfession::route('/create'),
            'edit'   => Pages\EditProfession::route('/{record}/edit'),
        ];
    }
}
