<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ExpenseLine extends Model
{
    use HasFactory, HasUuid;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
    ];

            protected static function booted()
    {
        static::created(fn ($model) => dispatch(new SyncModelJob($model)));
        static::updated(fn ($model) => dispatch(new SyncModelJob($model)));
        static::deleted(fn ($model) => dispatch(new SyncModelJob($model, 'delete')));
    }
}
