<?php

namespace App\Policies;

use App\Models\DocumentUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('share.permissions.show');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DocumentUser  $documentUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, DocumentUser $documentUser)
    {
        return $user->can('share.permissions.show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('share.permissions.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DocumentUser  $documentUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, DocumentUser $documentUser)
    {
        return $user->can('share.permissions.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DocumentUser  $documentUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, DocumentUser $documentUser)
    {
        return $user->can('share.permissions.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DocumentUser  $documentUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, DocumentUser $documentUser)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DocumentUser  $documentUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, DocumentUser $documentUser)
    {
        //
    }
}
