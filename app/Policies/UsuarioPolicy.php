<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;


class UsuarioPolicy
{
  use HandlesAuthorization;

  /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

  /**
   * Determine whether the user can view any models.
   *
   * @param  \App\Models\User  $user
   * @return mixed
   */
  public function viewAny(User $user)
  {
      return $user->can('usuario.show')
      ? Response::allow()
      : Response::deny('No tienes autorización para ver este recurso');
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param  \App\Models\User  $user
   * @return mixed
   */
  public function view(User $user)
  {
      return $user->can('usuario.show')
      ? Response::allow()
      : Response::deny('No tienes autorización para ver este recurso');
  }

  /**
   * Determine whether the user can create models.
   *
   * @param  \App\Models\User  $user
   * @return mixed
   */
  public function create(?User $user)
  {
      return $user->can('usuario.create')
      ? Response::allow()
      : Response::deny('No tienes autorización para crear este recurso');
  }

  /**
   * Determine whether the user can update the model.
   *
   * @param  \App\Models\User  $user
   * @return mixed
   */
  public function update(User $user)
  {
      return $user->can('usuario.update')
      ? Response::allow()
      : Response::deny('No tienes autorización para modificar este recurso');
  }

  /**
   * Determine whether the user can delete the model.
   *
   * @param  \App\Models\User  $user
   * @return mixed
   */
  public function delete(User $user)
  {
      return $user->can('usuario.delete')
      ? Response::allow()
      : Response::deny('No tienes autorización para eliminar este recurso');
  }

  public function import(User $user)
  {
      return $user->can('usuario.import')
      ? Response::allow()
      : Response::deny('No tienes autorización para este recurso');
  }

  public function export(User $user)
  {
      return $user->can('usuario.export')
      ? Response::allow()
      : Response::deny('No tienes autorización para este recurso');
  }

  public function aplicarbaja(User $user)
  {
      return $user->can('usuario.aplicar.baja')
      ? Response::allow()
      : Response::deny('No tienes autorización para este recurso');
  }

  public function revocarbaja(User $user)
  {
      return $user->can('usuario.revocar.baja')
      ? Response::allow()
      : Response::deny('No tienes autorización para este recurso');
  }

  /**
   * Determine whether the user can restore the model.
   *
   * @param  \App\Models\User  $user
   * @return mixed
   */
  public function restore(User $user)
  {
      //
  }

  /**
   * Determine whether the user can permanently delete the model.
   *
   * @param  \App\Models\User  $user
   * @return mixed
   */
  public function forceDelete(User $user)
  {
      //
  }
}
