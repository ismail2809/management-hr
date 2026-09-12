<?php

namespace App\Filament\Admin\Resources\EmployeeResource\Pages;

use App\Filament\Admin\Resources\EmployeeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    public static function authorizeResourceAccess(): void
    {
        if (auth()->user()?->isBasicRole()) {
            return;
        }

        parent::authorizeResourceAccess();
    }

    protected function authorizeAccess(): void
    {
        $user = auth()->user();

        if ($user?->isBasicRole()) {
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
        if (auth()->user()?->isBasicRole()) {
            return [];
        }

        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
