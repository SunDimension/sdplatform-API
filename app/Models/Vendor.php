<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    use HasFactory, HasUuid;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $table = "vendors";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'title',
        'designation',
        'contact_surname',
        'contact_firstname',
        'contact_middlename',
        'vendor_type',
        'service_type',
        'contact_phone_number',
        'contact_email',
        'image_url',
        'tin',
        'bank',
        'account_number',
        'account_name'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'vendor_type_id' => 'string',
        'bank_id' => 'string',
    ];

    public function vendorType(): BelongsTo
    {
        return $this->belongsTo(VendorType::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
    
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
