<?php

namespace App\Notifications;

use App\Models\EcoleSettings;
use Filament\Auth\Notifications\ResetPassword as FilamentResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends FilamentResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $ecole    = EcoleSettings::withoutGlobalScopes()->first();
        $nomEcole = $ecole?->nom_ecole ?? config('app.name');
        $expiration = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe — ' . $nomEcole)
            ->greeting('Bonjour ' . ($notifiable->name ?? '') . ',')
            ->line('Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
            ->action('Réinitialiser mon mot de passe', $this->url)
            ->line("Ce lien expirera dans **{$expiration} minutes**.")
            ->line("Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune action n'est requise.")
            ->salutation("Cordialement,\nRH Les Écoles Al Baraime");
    }
}
