<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AuthenticationMethod;

class AuthenticationMethodPolicy
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
        if($user->role->id === 1)
        {
            return true;
        }

        return false;
    }

    public function view(User $user, AuthenticationMethod $model)
    {
        // Authorization logic for viewing a model
        if($user->role->id === 1)
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

    public function update(User $user, AuthenticationMethod $model)
    {
        // Authorization logic for updating a model
        return false;
    }

    public function delete(User $user, AuthenticationMethod $model)
    {
        // Authorization logic for deleting a model
    }
}
