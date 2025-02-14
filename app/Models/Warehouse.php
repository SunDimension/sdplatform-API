<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasFactory;

        public $table = "warehouses";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'branch_id',
        'warehouse_address',
        'zipcode',
        'contact_person',
        'email',
        'phone',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'branch_id' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
