<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleManagementController extends Controller
{
    public function index()
    {
        return view('admin.roles.index');
    }

    // Data for DataTables
    public function data()
    {
        $roles = Role::select('id', 'name', 'guard_name');

        return DataTables::of($roles)
            ->addColumn('actions', function($role) {
                return view('admin.roles.partials.actions', ['role' => $role])->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    // Store new role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:50',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        return response()->json(['message' => 'Role created successfully.']);
    }

    // Edit role (fetch data)
    public function edit(Role $role)
    {
        return response()->json(['role' => $role]);
    }

    // Update role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|max:50|unique:roles,name,' . $role->id,
        ]);

        $role->update(['name' => $request->name]);

        return response()->json(['message' => 'Role updated successfully.']);
    }

    // Delete role
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully.']);
    }
}
