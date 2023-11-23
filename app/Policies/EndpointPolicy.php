<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Endpoint;

class EndpointPolicy
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
        if($user->role->id === 3)
        {
            return false;
        }

        return true;
    }

    public function view(User $user, Endpoint $model)
    {
        // Authorization logic for viewing a model
        if($user->role->id === 3)
        {
            return false;
        }

        return true;
    }

    public function create(User $user)
    {
        // Authorization logic for creating a model
        if($user->role->id === 3)
        {
            return false;
        }

        return true;
    }

    public function update(User $user, Endpoint $model)
    {
        // Authorization logic for updating a model
        if($user->role->id === 3)
        {
            return false;
        }

        return true;
    }

    public function delete(User $user, Endpoint $model)
    {
        // Authorization logic for deleting a model
        if($user->role->id === 3)
        {
            return false;
        }

        return true;
    }
}
