<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NatureDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class NatureDocumentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NatureDocument');
    }

    public function view(AuthUser $authUser, NatureDocument $natureDocument): bool
    {
        return $authUser->can('View:NatureDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NatureDocument');
    }

    public function update(AuthUser $authUser, NatureDocument $natureDocument): bool
    {
        return $authUser->can('Update:NatureDocument');
    }

    public function delete(AuthUser $authUser, NatureDocument $natureDocument): bool
    {
        return $authUser->can('Delete:NatureDocument');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:NatureDocument');
    }

    public function restore(AuthUser $authUser, NatureDocument $natureDocument): bool
    {
        return $authUser->can('Restore:NatureDocument');
    }

    public function forceDelete(AuthUser $authUser, NatureDocument $natureDocument): bool
    {
        return $authUser->can('ForceDelete:NatureDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NatureDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NatureDocument');
    }

    public function replicate(AuthUser $authUser, NatureDocument $natureDocument): bool
    {
        return $authUser->can('Replicate:NatureDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NatureDocument');
    }

}