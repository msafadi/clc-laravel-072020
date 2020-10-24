<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hasPermission($permission)
    {
        return (bool) DB::table('users_roles')
            ->join('roles_permissions', 'users_roles.role_id', '=', 'roles_permissions.role_id')
            ->where([
                'users_roles.user_id' => $this->id,
                'roles_permissions.permission' => $permission
            ])
            ->count();
    }

    public function cartProducts()
    {
        return $this->belongsToMany(Product::class, 'carts')
            ->using(Cart::class)
            ->withPivot(['quantity'])
            ->as('cart');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function routeNotificationForNexmo()
    {
        return $this->mobile;
    }

    public function routeNotificatioForMail()
    {
        return $this->email;
    }

    public function routeNotificatioForBroadcast()
    {
        return 'App.User.' . $this->id;
    }
}
