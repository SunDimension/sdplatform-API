<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\Syncable;
class ReleaseDetails extends Model
{
    use HasFactory, SoftDeletes, HasUuid, Syncable;

    protected $table = "release_details";

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [

        'release_id',
        'product_id',
        'release_quantity',
        'amount',
        'quantity_pieces',
        'unit_measurement',
    ];

    protected $cast = [

        'id' => 'integer',
        'release_id' => 'integer',
        'product_id' => 'integer',

    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(CreateItem::class, "product_id");
    }

    public function release()
    {
        return $this->belongsTo(Release::class);
    }
}
