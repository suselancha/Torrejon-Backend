<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {        
        $is_super_admin = $user->hasAnyRole(['Super-Admin']);

        $can_register_user = $user->can('register_user');

        return ($is_super_admin && $can_register_user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {        
        $is_super_admin = $user->hasAnyRole(['Super-Admin']);

        $can_edit_user = $user->can('edit_user');

        return ($is_super_admin && $can_edit_user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {        
        $is_super_admin = $user->hasAnyRole(['Super-Admin']);

        $can_delete_user = $user->can('delete_user');

        return ($is_super_admin && $can_delete_user);
    }

}
