<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        $user = auth()->user();

        if ($user && ! $user->hasRole('super-admin')) {
            // Redirect non-admin users to the app panel
            $this->redirect('/app');
            return null;
        }

        return $response;
    }
}
