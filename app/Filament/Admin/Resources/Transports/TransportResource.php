<?php

namespace App\Filament\Admin\Resources\Transports;

use App\Filament\Admin\Resources\Transports\Pages\CreateTransport;
use App\Filament\Admin\Resources\Transports\Pages\EditTransport;
use App\Filament\Admin\Resources\Transports\Pages\ListTransports;
use App\Filament\Admin\Resources\Transports\Schemas\TransportForm;
use App\Filament\Admin\Resources\Transports\Tables\TransportsTable;
use App\Filament\Admin\Concerns\HasCompanyField;
use App\Models\Transport;
use BackedEnum;
use App\Filament\Admin\Concerns\HasRoleBasedDelete;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransportResource extends Resource
{
    use HasRoleBasedDelete;

    use HasCompanyField;
    protected static ?string $model = Transport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;
    protected static ?string $navigationLabel = 'Transports';
    protected static ?string $modelLabel = 'Transport';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 23;

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return parent::getEloquentQuery()->withoutGlobalScopes();
        }

        return parent::getEloquentQuery();
    }

    public static function canViewAny(): bool { return auth()->user()?->hasRole('super-admin'); }

    public static function form(Schema $schema): Schema
    {
        return TransportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransportsTable::configure($table);
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
            'index' => ListTransports::route('/'),
            'create' => CreateTransport::route('/create'),
            'edit' => EditTransport::route('/{record}/edit'),
        ];
    }
}
