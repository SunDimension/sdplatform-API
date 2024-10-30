<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Requests\PermissionStoreRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    {
    }


    public function store(PermissionStoreRequest $request)
    {
        $permission = Permission::create($request->all());

        return  response(new PermissionResource($permission));
              
               
    }
    
    public function show(Permission $permission)
    {
        return new PermissionResource($permission);
    }

    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        $permission->update($request->all());

        return response(new PermissionResource($permission));
    }
    
    public function destroy($id) 
    {   
       
        Permission::destroy($id);

        
        return response()->noContent();
    }
}
