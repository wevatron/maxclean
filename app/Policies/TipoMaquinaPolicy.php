<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TipoMaquina;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoMaquinaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TipoMaquina');
    }

    public function view(AuthUser $authUser, TipoMaquina $tipoMaquina): bool
    {
        return $authUser->can('View:TipoMaquina');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoMaquina');
    }

    public function update(AuthUser $authUser, TipoMaquina $tipoMaquina): bool
    {
        return $authUser->can('Update:TipoMaquina');
    }

    public function delete(AuthUser $authUser, TipoMaquina $tipoMaquina): bool
    {
        return $authUser->can('Delete:TipoMaquina');
    }

    public function restore(AuthUser $authUser, TipoMaquina $tipoMaquina): bool
    {
        return $authUser->can('Restore:TipoMaquina');
    }

    public function forceDelete(AuthUser $authUser, TipoMaquina $tipoMaquina): bool
    {
        return $authUser->can('ForceDelete:TipoMaquina');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoMaquina');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoMaquina');
    }

    public function replicate(AuthUser $authUser, TipoMaquina $tipoMaquina): bool
    {
        return $authUser->can('Replicate:TipoMaquina');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoMaquina');
    }

}