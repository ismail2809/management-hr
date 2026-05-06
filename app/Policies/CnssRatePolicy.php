<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CnssRate;
use Illuminate\Auth\Access\HandlesAuthorization;

class CnssRatePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CnssRate');
    }

    public function view(AuthUser $authUser, CnssRate $cnssRate): bool
    {
        return $authUser->can('View:CnssRate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CnssRate');
    }

    public function update(AuthUser $authUser, CnssRate $cnssRate): bool
    {
        return $authUser->can('Update:CnssRate');
    }

    public function delete(AuthUser $authUser, CnssRate $cnssRate): bool
    {
        return $authUser->can('Delete:CnssRate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CnssRate');
    }

    public function restore(AuthUser $authUser, CnssRate $cnssRate): bool
    {
        return $authUser->can('Restore:CnssRate');
    }

    public function forceDelete(AuthUser $authUser, CnssRate $cnssRate): bool
    {
        return $authUser->can('ForceDelete:CnssRate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CnssRate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CnssRate');
    }

    public function replicate(AuthUser $authUser, CnssRate $cnssRate): bool
    {
        return $authUser->can('Replicate:CnssRate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CnssRate');
    }

}