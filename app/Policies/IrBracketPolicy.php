<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\IrBracket;
use Illuminate\Auth\Access\HandlesAuthorization;

class IrBracketPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:IrBracket');
    }

    public function view(AuthUser $authUser, IrBracket $irBracket): bool
    {
        return $authUser->can('View:IrBracket');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:IrBracket');
    }

    public function update(AuthUser $authUser, IrBracket $irBracket): bool
    {
        return $authUser->can('Update:IrBracket');
    }

    public function delete(AuthUser $authUser, IrBracket $irBracket): bool
    {
        return $authUser->can('Delete:IrBracket');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:IrBracket');
    }

    public function restore(AuthUser $authUser, IrBracket $irBracket): bool
    {
        return $authUser->can('Restore:IrBracket');
    }

    public function forceDelete(AuthUser $authUser, IrBracket $irBracket): bool
    {
        return $authUser->can('ForceDelete:IrBracket');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:IrBracket');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:IrBracket');
    }

    public function replicate(AuthUser $authUser, IrBracket $irBracket): bool
    {
        return $authUser->can('Replicate:IrBracket');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:IrBracket');
    }

}