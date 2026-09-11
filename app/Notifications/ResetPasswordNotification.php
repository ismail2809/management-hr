<?php

namespace App\Notifications;

use App\Models\EcoleSettings;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Route;

class ResetPasswordNotification extends BaseResetPassword
{
    protected function resetUrl($notifiable): string
    {
        // Utiliser la route Filament du panel principal (app)
        if (Route::has('filament.app.auth.password-reset.reset')) {
            return url(route('filament.app.auth.password-reset.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        }
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    public function toMail($notifiable): MailMessage
    {
        $ecole = EcoleSettings::first();
        $nomEcole = $ecole?->nom_ecole ?? config('app.name');
        $expiration = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe — ' . $nomEcole)
            ->greeting('Bonjour ' . ($notifiable->name ?? '') . ',')
            ->line('Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
            ->action('Réinitialiser mon mot de passe', $this->resetUrl($notifiable))
            ->line("Ce lien expirera dans **{$expiration} minutes**.")
            ->line("Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune action n'est requise.")
            ->salutation("Cordialement,\n{$nomEcole}");
    }
}
