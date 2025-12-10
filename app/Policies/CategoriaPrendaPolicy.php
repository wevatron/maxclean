<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CategoriaPrenda;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaPrendaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CategoriaPrenda');
    }

    public function view(AuthUser $authUser, CategoriaPrenda $categoriaPrenda): bool
    {
        return $authUser->can('View:CategoriaPrenda');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CategoriaPrenda');
    }

    public function update(AuthUser $authUser, CategoriaPrenda $categoriaPrenda): bool
    {
        return $authUser->can('Update:CategoriaPrenda');
    }

    public function delete(AuthUser $authUser, CategoriaPrenda $categoriaPrenda): bool
    {
        return $authUser->can('Delete:CategoriaPrenda');
    }

    public function restore(AuthUser $authUser, CategoriaPrenda $categoriaPrenda): bool
    {
        return $authUser->can('Restore:CategoriaPrenda');
    }

    public function forceDelete(AuthUser $authUser, CategoriaPrenda $categoriaPrenda): bool
    {
        return $authUser->can('ForceDelete:CategoriaPrenda');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CategoriaPrenda');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CategoriaPrenda');
    }

    public function replicate(AuthUser $authUser, CategoriaPrenda $categoriaPrenda): bool
    {
        return $authUser->can('Replicate:CategoriaPrenda');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CategoriaPrenda');
    }

}