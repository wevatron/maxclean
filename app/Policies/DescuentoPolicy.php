<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Descuento;
use Illuminate\Auth\Access\HandlesAuthorization;

class DescuentoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Descuento');
    }

    public function view(AuthUser $authUser, Descuento $descuento): bool
    {
        return $authUser->can('View:Descuento');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Descuento');
    }

    public function update(AuthUser $authUser, Descuento $descuento): bool
    {
        return $authUser->can('Update:Descuento');
    }

    public function delete(AuthUser $authUser, Descuento $descuento): bool
    {
        return $authUser->can('Delete:Descuento');
    }

    public function restore(AuthUser $authUser, Descuento $descuento): bool
    {
        return $authUser->can('Restore:Descuento');
    }

    public function forceDelete(AuthUser $authUser, Descuento $descuento): bool
    {
        return $authUser->can('ForceDelete:Descuento');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Descuento');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Descuento');
    }

    public function replicate(AuthUser $authUser, Descuento $descuento): bool
    {
        return $authUser->can('Replicate:Descuento');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Descuento');
    }

}