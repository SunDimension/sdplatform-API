<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeploymentRequest extends Model
{
    protected $fillable = [
        'field_url',
        'deploy_token_hash',  // ← This was missing!
        'status',
        'response',
        'requested_at',
        'completed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
