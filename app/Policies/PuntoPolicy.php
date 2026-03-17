<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Punto;
use Illuminate\Auth\Access\HandlesAuthorization;

class PuntoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Punto');
    }

    public function view(AuthUser $authUser, Punto $punto): bool
    {
        return $authUser->can('View:Punto');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Punto');
    }

    public function update(AuthUser $authUser, Punto $punto): bool
    {
        return $authUser->can('Update:Punto');
    }

    public function delete(AuthUser $authUser, Punto $punto): bool
    {
        return $authUser->can('Delete:Punto');
    }

    public function restore(AuthUser $authUser, Punto $punto): bool
    {
        return $authUser->can('Restore:Punto');
    }

    public function forceDelete(AuthUser $authUser, Punto $punto): bool
    {
        return $authUser->can('ForceDelete:Punto');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Punto');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Punto');
    }

    public function replicate(AuthUser $authUser, Punto $punto): bool
    {
        return $authUser->can('Replicate:Punto');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Punto');
    }

}