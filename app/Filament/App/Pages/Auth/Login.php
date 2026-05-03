<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        $this->redirect('/admin/login');
    }
}
