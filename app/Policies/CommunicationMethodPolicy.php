<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CommunicationMethod;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunicationMethodPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CommunicationMethod');
    }

    public function view(AuthUser $authUser, CommunicationMethod $communicationMethod): bool
    {
        return $authUser->can('View:CommunicationMethod');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CommunicationMethod');
    }

    public function update(AuthUser $authUser, CommunicationMethod $communicationMethod): bool
    {
        return $authUser->can('Update:CommunicationMethod');
    }

    public function delete(AuthUser $authUser, CommunicationMethod $communicationMethod): bool
    {
        return $authUser->can('Delete:CommunicationMethod');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CommunicationMethod');
    }

    public function restore(AuthUser $authUser, CommunicationMethod $communicationMethod): bool
    {
        return $authUser->can('Restore:CommunicationMethod');
    }

    public function forceDelete(AuthUser $authUser, CommunicationMethod $communicationMethod): bool
    {
        return $authUser->can('ForceDelete:CommunicationMethod');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CommunicationMethod');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CommunicationMethod');
    }

    public function replicate(AuthUser $authUser, CommunicationMethod $communicationMethod): bool
    {
        return $authUser->can('Replicate:CommunicationMethod');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CommunicationMethod');
    }

}