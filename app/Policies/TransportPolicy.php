<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Transport;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Transport');
    }

    public function view(AuthUser $authUser, Transport $transport): bool
    {
        return $authUser->can('View:Transport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Transport');
    }

    public function update(AuthUser $authUser, Transport $transport): bool
    {
        return $authUser->can('Update:Transport');
    }

    public function delete(AuthUser $authUser, Transport $transport): bool
    {
        return $authUser->can('Delete:Transport');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Transport');
    }

    public function restore(AuthUser $authUser, Transport $transport): bool
    {
        return $authUser->can('Restore:Transport');
    }

    public function forceDelete(AuthUser $authUser, Transport $transport): bool
    {
        return $authUser->can('ForceDelete:Transport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Transport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Transport');
    }

    public function replicate(AuthUser $authUser, Transport $transport): bool
    {
        return $authUser->can('Replicate:Transport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Transport');
    }

}