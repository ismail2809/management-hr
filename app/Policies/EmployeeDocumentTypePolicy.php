<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EmployeeDocumentType;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeDocumentTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmployeeDocumentType');
    }

    public function view(AuthUser $authUser, EmployeeDocumentType $employeeDocumentType): bool
    {
        return $authUser->can('View:EmployeeDocumentType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmployeeDocumentType');
    }

    public function update(AuthUser $authUser, EmployeeDocumentType $employeeDocumentType): bool
    {
        return $authUser->can('Update:EmployeeDocumentType');
    }

    public function delete(AuthUser $authUser, EmployeeDocumentType $employeeDocumentType): bool
    {
        return $authUser->can('Delete:EmployeeDocumentType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EmployeeDocumentType');
    }

    public function restore(AuthUser $authUser, EmployeeDocumentType $employeeDocumentType): bool
    {
        return $authUser->can('Restore:EmployeeDocumentType');
    }

    public function forceDelete(AuthUser $authUser, EmployeeDocumentType $employeeDocumentType): bool
    {
        return $authUser->can('ForceDelete:EmployeeDocumentType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmployeeDocumentType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmployeeDocumentType');
    }

    public function replicate(AuthUser $authUser, EmployeeDocumentType $employeeDocumentType): bool
    {
        return $authUser->can('Replicate:EmployeeDocumentType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmployeeDocumentType');
    }

}