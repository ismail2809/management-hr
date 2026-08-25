<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NiveauScolaire;
use Illuminate\Auth\Access\HandlesAuthorization;

class NiveauScolairePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NiveauScolaire');
    }

    public function view(AuthUser $authUser, NiveauScolaire $niveauScolaire): bool
    {
        return $authUser->can('View:NiveauScolaire');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NiveauScolaire');
    }

    public function update(AuthUser $authUser, NiveauScolaire $niveauScolaire): bool
    {
        return $authUser->can('Update:NiveauScolaire');
    }

    public function delete(AuthUser $authUser, NiveauScolaire $niveauScolaire): bool
    {
        return $authUser->can('Delete:NiveauScolaire');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:NiveauScolaire');
    }

    public function restore(AuthUser $authUser, NiveauScolaire $niveauScolaire): bool
    {
        return $authUser->can('Restore:NiveauScolaire');
    }

    public function forceDelete(AuthUser $authUser, NiveauScolaire $niveauScolaire): bool
    {
        return $authUser->can('ForceDelete:NiveauScolaire');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NiveauScolaire');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NiveauScolaire');
    }

    public function replicate(AuthUser $authUser, NiveauScolaire $niveauScolaire): bool
    {
        return $authUser->can('Replicate:NiveauScolaire');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NiveauScolaire');
    }

}