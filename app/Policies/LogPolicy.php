<?php

namespace App\Policies;

use App\Models\Log;
use App\Models\Role;
use App\Models\User;

class LogPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Log $model)
    {
        // Authorization logic for viewing a model
        if($user->role === Role::ROLE_SUPERADMIN)
        {
            return true;
        }
        return false;
    }

    public function create(User $user)
    {
        // Authorization logic for creating a model
        return false;
    }

    public function update(User $user, Log $model)
    {
        // Authorization logic for updating a model
        return false;
    }

    public function delete(User $user, Log $model)
    {
        // Authorization logic for deleting a model
        return false;
    }
}
