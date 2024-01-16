<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ConnectionLog;

class ConnectionLogPolicy
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

    public function view(User $user, ConnectionLog $model)
    {
        // Authorization logic for viewing a model
        return true;
    }

    public function create(User $user)
    {
        // Authorization logic for creating a model
        return false;
    }

    public function update(User $user, ConnectionLog $model)
    {
        // Authorization logic for updating a model
        return true;
    }

    public function delete(User $user, ConnectionLog $model)
    {
        // Authorization logic for deleting a model
        return false;
    }
}
