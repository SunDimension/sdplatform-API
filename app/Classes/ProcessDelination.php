<?php

use App\Models\Branch;
use Illuminate\Support\Facades\Gate;

class ProcessDelination 
{

    public static function partitionUserData($query, $branch_id, $permission_set)
    {
        if (Gate::allows($permission_set[0])) {
           
        }
        elseif (Gate::allows($permission_set[1])) {
            $region_id = Branch::where('id', $branch_id)->first()->region_id;
            $branchIds = Branch::where('region_id', $region_id)->get()->pluck('id');
            $query->whereIn('branch_id', $branchIds);
 
        } elseif (Gate::allows($permission_set[2])) {
            $query->where('branch_id', $branch_id); // Filter by branch_id (user's branch)
            // return new CustomerCollection($customers);
        }
        else{
            $query->where('branch_id', -1);
        }

        return $query;

    }
}