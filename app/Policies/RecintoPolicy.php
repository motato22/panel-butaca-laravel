<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Recinto;

class RecintoPolicy
{
    /**
     * Determina si el usuario puede crear un recinto.
     */
    public function create(User $user)
    {
        // Solo los usuarios con el rol ROLE_ADMIN pueden crear recintos
        return $user->hasRole('ROLE_ADMIN');
    }

    /**
     * Determina si el usuario puede actualizar un recinto.
     */
    public function update(User $user, Recinto $recinto)
    {
        // Solo los usuarios con el rol ROLE_ADMIN o los dueños del recinto pueden actualizarlo
        return $user->hasRole('ROLE_ADMIN') || $recinto->users->contains($user);
    }

    /**
     * Determina si el usuario puede eliminar un recinto.
     */
    public function delete(User $user, Recinto $recinto)
    {
        // Solo los usuarios con el rol ROLE_ADMIN pueden eliminar recintos
        return $user->hasRole('ROLE_ADMIN');
    }
}