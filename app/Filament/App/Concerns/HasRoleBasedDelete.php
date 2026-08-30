<?php

namespace App\Filament\App\Concerns;

/**
 * Restrict delete actions to super-admin and directeur only.
 * All deletes are soft-deletes (rows remain in DB).
 */
trait HasRoleBasedDelete
{
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'directeur']);
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'directeur']);
    }

    public static function canForceDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // personne ne peut supprimer définitivement
    }

    public static function canForceDeleteAny(): bool
    {
        return false;
    }
}
