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

}