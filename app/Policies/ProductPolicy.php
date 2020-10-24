<?php

namespace App\Policies;

use App\Product;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class ProductPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->type == 'super-admin') {
            //return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // user id, permission
        // SELECT count(*) from users_roles inner join roles_permissions
        // on users_roles.role_id = roles_permissions.role_id
        // WHERE users_roles.user_id = ? AND roles_permissions.permission = ?

        return $user->hasPermission('products.view-any');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function view(User $user, Product $product)
    {
        //
        if ($user->id != $product->user_id) {
            return false;
        }

        return $user->hasPermission('products.view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        return $user->hasPermission('products.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function update(User $user, Product $product)
    {
        /*if ($user->id != $product->user_id) {
            return false;
        }*/
        return $user->hasPermission('products.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function delete(User $user, Product $product)
    {
        /*if ($user->id != $product->user_id) {
            return false;
        }*/
        return $user->hasPermission('products.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function restore(User $user, Product $product)
    {
        /*if ($user->id != $product->user_id) {
            return false;
        }*/
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function forceDelete(User $user, Product $product)
    {
        if ($user->id != $product->user_id) {
            return false;
        }
        //
    }

    public function images(User $user, Product $product)
    {

    }
}
