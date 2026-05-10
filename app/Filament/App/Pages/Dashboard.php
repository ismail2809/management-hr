<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public function mountCanAuthorizeAccess(): void
    {
        if (auth()->user()?->hasRole('employee')) {
            $this->redirect(MonEspace::getUrl());
            return;
        }

        parent::mountCanAuthorizeAccess();
    }
}
