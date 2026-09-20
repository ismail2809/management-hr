<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DocumentRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DocumentRequest');
    }

    public function view(AuthUser $authUser, DocumentRequest $documentRequest): bool
    {
        return $authUser->can('View:DocumentRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DocumentRequest');
    }

    public function update(AuthUser $authUser, DocumentRequest $documentRequest): bool
    {
        return $authUser->can('Update:DocumentRequest');
    }

    public function delete(AuthUser $authUser, DocumentRequest $documentRequest): bool
    {
        return $authUser->can('Delete:DocumentRequest');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DocumentRequest');
    }

}