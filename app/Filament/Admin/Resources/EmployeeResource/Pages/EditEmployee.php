<?php

namespace App\Filament\App\Resources\EmployeeResource\Pages;

use App\Filament\App\Resources\EmployeeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    public static function authorizeResourceAccess(): void
    {
        if (auth()->user()?->hasRole('employee')) {
            return;
        }

        parent::authorizeResourceAccess();
    }

    protected function authorizeAccess(): void
    {
        $user = auth()->user();

        if ($user?->hasRole('employee')) {
            abort_unless(
                $user->employee_id && (int) $this->record->id === (int) $user->employee_id,
                403
            );
            return;
        }

        parent::authorizeAccess();
    }

    protected function getHeaderActions(): array
    {
        if (auth()->user()?->hasRole('employee')) {
            return [];
        }

        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
