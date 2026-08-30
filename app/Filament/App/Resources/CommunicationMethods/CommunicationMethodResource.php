<?php

namespace App\Filament\App\Resources\CommunicationMethods;

use App\Filament\App\Resources\CommunicationMethods\Pages\CreateCommunicationMethod;
use App\Filament\App\Resources\CommunicationMethods\Pages\EditCommunicationMethod;
use App\Filament\App\Resources\CommunicationMethods\Pages\ListCommunicationMethods;
use App\Filament\App\Resources\CommunicationMethods\Schemas\CommunicationMethodForm;
use App\Filament\App\Resources\CommunicationMethods\Tables\CommunicationMethodsTable;
use App\Models\CommunicationMethod;
use App\Filament\App\Concerns\HasRoleBasedDelete;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CommunicationMethodResource extends Resource
{
    use HasRoleBasedDelete;

    protected static ?string $model = CommunicationMethod::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Modes de communication';
    protected static ?string $modelLabel = 'Mode de communication';
    protected static ?string $pluralModelLabel = 'Modes de communication';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 30;

    public static function canViewAny(): bool { return ! auth()->user()?->hasRole('employee'); }

    public static function form(Schema $schema): Schema
    {
        return CommunicationMethodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunicationMethodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommunicationMethods::route('/'),
            'create' => CreateCommunicationMethod::route('/create'),
            'edit' => EditCommunicationMethod::route('/{record}/edit'),
        ];
    }
}
