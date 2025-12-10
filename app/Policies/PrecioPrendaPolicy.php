<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrecioPrenda;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrecioPrendaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrecioPrenda');
    }

    public function view(AuthUser $authUser, PrecioPrenda $precioPrenda): bool
    {
        return $authUser->can('View:PrecioPrenda');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrecioPrenda');
    }

    public function update(AuthUser $authUser, PrecioPrenda $precioPrenda): bool
    {
        return $authUser->can('Update:PrecioPrenda');
    }

    public function delete(AuthUser $authUser, PrecioPrenda $precioPrenda): bool
    {
        return $authUser->can('Delete:PrecioPrenda');
    }

    public function restore(AuthUser $authUser, PrecioPrenda $precioPrenda): bool
    {
        return $authUser->can('Restore:PrecioPrenda');
    }

    public function forceDelete(AuthUser $authUser, PrecioPrenda $precioPrenda): bool
    {
        return $authUser->can('ForceDelete:PrecioPrenda');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrecioPrenda');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrecioPrenda');
    }

    public function replicate(AuthUser $authUser, PrecioPrenda $precioPrenda): bool
    {
        return $authUser->can('Replicate:PrecioPrenda');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrecioPrenda');
    }

}