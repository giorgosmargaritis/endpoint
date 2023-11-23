<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
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
        return false;
    }

    public function view(User $user)
    {
        return false;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user)
    {
        return false;
    }

    public function delete(User $user)
    {
        return false;
    }
}
