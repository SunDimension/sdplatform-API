<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, HasRoles, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'name',
        'email',
        'password',
        'store_id',
        'branch_id',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'branch_id' => 'string',
        'password' => 'hashed',
        'store_id' => 'string'
    ];


   

  
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // public function store()
    // {
    //     return $this->belongsTo(Store::class);
    // }

    // public function status(): BelongsTo
    // {
    //     return $this->belongsTo(Status::class);
    // }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }



    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function hasPermissionTo($name)
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $name)) {
                return true;
            }
        }
        return false;
    }
    //
}
