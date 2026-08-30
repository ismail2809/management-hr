<?php

namespace App\Filament\App\Resources\DocumentTypes;

use App\Filament\App\Resources\DocumentTypes\Pages\CreateDocumentType;
use App\Filament\App\Resources\DocumentTypes\Pages\EditDocumentType;
use App\Filament\App\Resources\DocumentTypes\Pages\ListDocumentTypes;
use App\Filament\App\Resources\DocumentTypes\Schemas\DocumentTypeForm;
use App\Filament\App\Resources\DocumentTypes\Tables\DocumentTypesTable;
use App\Models\DocumentType;
use App\Filament\App\Concerns\HasRoleBasedDelete;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DocumentTypeResource extends Resource
{
    use HasRoleBasedDelete;

    protected static ?string $model = DocumentType::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $slug = 'types-de-demande';
    protected static ?string $navigationLabel = 'Types de demande';
    protected static ?string $modelLabel = 'Type de demande';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 25;

    public static function canViewAny(): bool { return ! auth()->user()?->hasRole('employee'); }

    public static function form(Schema $schema): Schema
    {
        return DocumentTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentTypes::route('/'),
            'create' => CreateDocumentType::route('/create'),
            'edit' => EditDocumentType::route('/{record}/edit'),
        ];
    }
}
