<?php

namespace App\Filament\App\Resources\Transports;

use App\Filament\App\Resources\Transports\Pages\CreateTransport;
use App\Filament\App\Resources\Transports\Pages\EditTransport;
use App\Filament\App\Resources\Transports\Pages\ListTransports;
use App\Filament\App\Resources\Transports\Schemas\TransportForm;
use App\Filament\App\Resources\Transports\Tables\TransportsTable;
use App\Filament\App\Concerns\HasCompanyField;
use App\Models\Transport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransportResource extends Resource
{
    use HasCompanyField;
    protected static ?string $model = Transport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;
    protected static ?string $navigationLabel = 'Transports';
    protected static ?string $modelLabel = 'Transport';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 23;

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
