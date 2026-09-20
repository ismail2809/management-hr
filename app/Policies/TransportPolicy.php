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

}