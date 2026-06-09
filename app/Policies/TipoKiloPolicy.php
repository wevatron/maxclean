<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TipoKilo;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoKiloPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TipoKilo');
    }

    public function view(AuthUser $authUser, TipoKilo $tipoKilo): bool
    {
        return $authUser->can('View:TipoKilo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoKilo');
    }

    public function update(AuthUser $authUser, TipoKilo $tipoKilo): bool
    {
        return $authUser->can('Update:TipoKilo');
    }

    public function delete(AuthUser $authUser, TipoKilo $tipoKilo): bool
    {
        return $authUser->can('Delete:TipoKilo');
    }

    public function restore(AuthUser $authUser, TipoKilo $tipoKilo): bool
    {
        return $authUser->can('Restore:TipoKilo');
    }

    public function forceDelete(AuthUser $authUser, TipoKilo $tipoKilo): bool
    {
        return $authUser->can('ForceDelete:TipoKilo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoKilo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoKilo');
    }

    public function replicate(AuthUser $authUser, TipoKilo $tipoKilo): bool
    {
        return $authUser->can('Replicate:TipoKilo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoKilo');
    }

}