<?php

namespace App\Classes;

use App\Models\Branch;
use Illuminate\Support\Facades\Gate;

class ProcessDelination 
{
    public static function partitionUserData($query, $branch_id, $permission_set)
    {
        if (Gate::allows($permission_set[0])) {
            return $query; // User can view all sales orders
        }

        if (Gate::allows($permission_set[1])) {
            $region_id = Branch::where('id', $branch_id)->value('region_id');
            $branchIds = Branch::where('region_id', $region_id)->pluck('id')->toArray();
            return $query->whereIn('branch_id', $branchIds);
        }

        if (Gate::allows($permission_set[2])) {
            return $query->where('branch_id', $branch_id);
        }

        return $query->where('branch_id', -1); // No access
    }
}
