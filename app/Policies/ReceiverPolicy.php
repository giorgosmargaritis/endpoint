<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Receiver;

class ReceiverPolicy
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

    public function view(User $user, Receiver $model)
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

    public function update(User $user, Receiver $model)
    {
        // Authorization logic for updating a model
        if($user->role->id === 3)
        {
            return false;
        }

        return true;
    }

    public function delete(User $user, Receiver $model)
    {
        // Authorization logic for deleting a model
        if($user->role->id === 3)
        {
            return false;
        }

        return true;
    }
}
