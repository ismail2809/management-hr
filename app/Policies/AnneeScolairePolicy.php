<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AnneeScolaire;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnneeScolairePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AnneeScolaire');
    }

    public function view(AuthUser $authUser, AnneeScolaire $anneeScolaire): bool
    {
        return $authUser->can('View:AnneeScolaire');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AnneeScolaire');
    }

    public function update(AuthUser $authUser, AnneeScolaire $anneeScolaire): bool
    {
        return $authUser->can('Update:AnneeScolaire');
    }

    public function delete(AuthUser $authUser, AnneeScolaire $anneeScolaire): bool
    {
        return $authUser->can('Delete:AnneeScolaire');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AnneeScolaire');
    }

    public function restore(AuthUser $authUser, AnneeScolaire $anneeScolaire): bool
    {
        return $authUser->can('Restore:AnneeScolaire');
    }

    public function forceDelete(AuthUser $authUser, AnneeScolaire $anneeScolaire): bool
    {
        return $authUser->can('ForceDelete:AnneeScolaire');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AnneeScolaire');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AnneeScolaire');
    }

    public function replicate(AuthUser $authUser, AnneeScolaire $anneeScolaire): bool
    {
        return $authUser->can('Replicate:AnneeScolaire');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AnneeScolaire');
    }

}