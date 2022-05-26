<?php

namespace App\Policies;

use App\Helpers\Dixa;
use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
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
        return $user->can('document.show');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $documentos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Document $document)
    {
        $isSameDepartment = $user->department->id === $document->department->id;
        return $user->can('document.show') && $isSameDepartment;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->hasRole('Head of Department')) {
            return $user->can('document.create');
        } elseif ($user->hasRole('Analyst')) {
            return $user->can(Dixa::ANALYST_WRITE_PERMISSION);
        }
        return $user->can('document.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $documentos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Document $document)
    {
        $isSameDepartment = $user->department->id === $document->department->id;
        if ($user->hasRole('Head of Department')) {
            return $user->can('document.update') && $isSameDepartment;
        } elseif ($user->hasRole('Analyst')) {
            return $user->can(Dixa::ANALYST_WRITE_PERMISSION) && $isSameDepartment;
        }
        return $user->can('document.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $documentos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Document $document)
    {
        $isSameDepartment = $user->department->id === $document->department->id;
        if ($user->hasRole('Head of Department')) {
            return $user->can('document.delete') && $isSameDepartment;
        } elseif ($user->hasRole('Analyst')) {
            return $user->can(Dixa::ANALYST_WRITE_PERMISSION) && $isSameDepartment;
        }
        return $user->can('document.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $documentos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Document $documentos)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $documentos
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Document $documentos)
    {
        //
    }
}
