<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Groupe;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Groupe');
    }

    public function view(AuthUser $authUser, Groupe $groupe): bool
    {
        return $authUser->can('View:Groupe');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Groupe');
    }

    public function update(AuthUser $authUser, Groupe $groupe): bool
    {
        return $authUser->can('Update:Groupe');
    }

    public function delete(AuthUser $authUser, Groupe $groupe): bool
    {
        return $authUser->can('Delete:Groupe');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Groupe');
    }

    public function restore(AuthUser $authUser, Groupe $groupe): bool
    {
        return $authUser->can('Restore:Groupe');
    }

    public function forceDelete(AuthUser $authUser, Groupe $groupe): bool
    {
        return $authUser->can('ForceDelete:Groupe');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Groupe');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Groupe');
    }

    public function replicate(AuthUser $authUser, Groupe $groupe): bool
    {
        return $authUser->can('Replicate:Groupe');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Groupe');
    }

}