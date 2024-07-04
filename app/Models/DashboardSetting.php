<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardSetting extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chart_id',
        'module_id',
        'chart_type_id',
        'chart_category_id',
        'chart_title',
        'is_active',
        'order_by',
        'is_group',
        'submodule_Id',
        'add_condition',
        'created_by',
        'modified_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'chart_id' => 'integer',
        'module_id' => 'integer',
        'chart_type_id' => 'integer',
        'chart_category_id' => 'integer',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    public function chart(): BelongsTo
    {
        return $this->belongsTo(Chart::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function chartType(): BelongsTo
    {
        return $this->belongsTo(ChartType::class);
    }

    public function chartCategory(): BelongsTo
    {
        return $this->belongsTo(ChartCategory::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employees,id::class);
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(Employees,id::class);
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(Employees,id::class);
    }
}
