<?php

namespace App\Filament\Admin\Resources;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as ShieldRoleResource;
use Spatie\Permission\Models\Role;

class RoleResource extends ShieldRoleResource
{
    public static function getNavigationBadge(): ?string
    {
        return (string) Role::count();
    }
}
