<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Declaration;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeclarationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Declaration');
    }

    public function view(AuthUser $authUser, Declaration $declaration): bool
    {
        return $authUser->can('View:Declaration');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Declaration');
    }

    public function update(AuthUser $authUser, Declaration $declaration): bool
    {
        return $authUser->can('Update:Declaration');
    }

    public function delete(AuthUser $authUser, Declaration $declaration): bool
    {
        return $authUser->can('Delete:Declaration');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Declaration') && $authUser->hasAnyRole(['admin', 'super-admin']);
    }

    public function restore(AuthUser $authUser, Declaration $declaration): bool
    {
        return $authUser->can('Restore:Declaration');
    }

    public function forceDelete(AuthUser $authUser, Declaration $declaration): bool
    {
        return $authUser->can('ForceDelete:Declaration');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Declaration');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Declaration');
    }

    public function replicate(AuthUser $authUser, Declaration $declaration): bool
    {
        return $authUser->can('Replicate:Declaration');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Declaration');
    }

}