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
use App\Models\Concerns\Syncable;
use App\Models\Branch;
use App\Models\Store;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, HasRoles, HasUuids, Syncable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'password',
        'store_id',
        'branch_id',
        'sync_id',
        'location_id',
        'sync_status',
        'sync_version',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_error',
        'id'

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



    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate UUID for id if not set
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

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
