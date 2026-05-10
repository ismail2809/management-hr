<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function mount(): void
    {
        if (auth()->user()?->hasRole('employee')) {
            $this->redirect(MonEspace::getUrl());
        }
    }
}
