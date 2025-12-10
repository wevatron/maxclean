<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Prenda;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrendaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Prenda');
    }

    public function view(AuthUser $authUser, Prenda $prenda): bool
    {
        return $authUser->can('View:Prenda');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Prenda');
    }

    public function update(AuthUser $authUser, Prenda $prenda): bool
    {
        return $authUser->can('Update:Prenda');
    }

    public function delete(AuthUser $authUser, Prenda $prenda): bool
    {
        return $authUser->can('Delete:Prenda');
    }

    public function restore(AuthUser $authUser, Prenda $prenda): bool
    {
        return $authUser->can('Restore:Prenda');
    }

    public function forceDelete(AuthUser $authUser, Prenda $prenda): bool
    {
        return $authUser->can('ForceDelete:Prenda');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Prenda');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Prenda');
    }

    public function replicate(AuthUser $authUser, Prenda $prenda): bool
    {
        return $authUser->can('Replicate:Prenda');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Prenda');
    }

}