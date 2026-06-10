<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function getRoles(Request $request)
    {
        $roles = Role::all();
        return $roles;
    }

    public function getPermissions(Request $request)
    {
        $permissions = Permission::all();
        return $permissions;
    }
}
