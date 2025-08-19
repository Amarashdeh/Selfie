<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index()
    {
        $roles = Role::pluck('name');
        return view('admin.users.index', compact('roles'));
    }

    public function data(Request $request)
    {
        $trashedFilter = filter_var($request->query('trashed', 0), FILTER_VALIDATE_BOOLEAN);

        $currentUser = null;
        $currentGuard = null;
        $currentRoles = [];

        // Get current logged-in user safely
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $currentGuard = 'admin';
            $currentRoles = $currentUser ? $currentUser->roles->pluck('name')->toArray() : [];
        } elseif (Auth::guard('web')->check()) {
            $currentUser = Auth::guard('web')->user();
            $currentGuard = 'web';
            $currentRoles = $currentUser ? $currentUser->roles->pluck('name')->toArray() : [];
        }

        // USERS
        $usersQuery = User::with('roles')->select('id','name','email','deleted_at');
        if ($trashedFilter) $usersQuery->onlyTrashed();

        // ADMINS
        $adminsQuery = Admin::with('roles')->select('id','name','email','deleted_at');
        if ($trashedFilter) $adminsQuery->onlyTrashed();

        // Apply restrictions for Admin (exclude SuperAdmin)
        if ($currentUser && in_array('Admin', $currentRoles) && !in_array('SuperAdmin', $currentRoles)) {
            $adminsQuery->whereDoesntHave('roles', function($q) {
                $q->where('name', 'SuperAdmin');
            });
            $usersQuery->whereDoesntHave('roles', function($q) {
                $q->where('name', 'SuperAdmin');
            });
        }

        // Exclude the current user themselves
        if ($currentUser) {
            if ($currentGuard === 'admin') {
                $adminsQuery->where('id', '!=', $currentUser->id);
            } else {
                $usersQuery->where('id', '!=', $currentUser->id);
            }
        }

        $users = $usersQuery->get()->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->roles ? $u->roles->pluck('name')->first() ?? '-' : '-',
                'type' => 'user',
                'trashed' => $u->trashed(),
            ];
        });

        $admins = $adminsQuery->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'name' => $a->name,
                'email' => $a->email,
                'role' => $a->roles ? $a->roles->pluck('name')->first() ?? '-' : '-',
                'type' => 'admin',
                'trashed' => $a->trashed(),
            ];
        });

        $combined = $users->concat($admins);

        // Return JSON safely
        return DataTables::collection($combined)
            ->addColumn('actions', function($item){
                try {
                    return view('admin.users.partials.actions',['user'=>$item])->render();
                } catch (\Throwable $e) {
                    return ''; // fallback if view fails
                }
            })
            ->rawColumns(['actions'])
            ->toJson();
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255','regex:/^[A-Za-z0-9\s\-\_\.]+$/'],
            'email' => ['required','email'],
            'password' => ['required','string','min:8','confirmed'],
            'role' => ['required','string','exists:roles,name'],
        ]);

        $adminRoles = ['Admin', 'SuperAdmin'];

        if (in_array($validated['role'], $adminRoles)) {
            if (Admin::where('email', $validated['email'])->exists()) {
                return response()->json(['errors'=>['email'=>['Email already exists for an admin user.']]],422);
            }
            $model = Admin::create([
                'name'=>$validated['name'],
                'email'=>$validated['email'],
                'password'=>Hash::make($validated['password']),
            ]);
        } else {
            if (User::where('email', $validated['email'])->exists()) {
                return response()->json(['errors'=>['email'=>['Email already exists for a regular user.']]],422);
            }
            $model = User::create([
                'name'=>$validated['name'],
                'email'=>$validated['email'],
                'password'=>Hash::make($validated['password']),
            ]);
        }

        $model->syncRoles([$validated['role']]);
        return response()->json(['message'=>'User created successfully']);
    }

    public function edit($type, $id)
    {
        if ($type === 'admin') {
            $model = Admin::withTrashed()->findOrFail($id);
        } else {
            $model = User::withTrashed()->findOrFail($id);
        }

        if ($model->trashed() && !auth()->user()->hasRole('SuperAdmin')) {
            abort(403, 'You cannot edit a deleted user.');
        }

        // Get all roles from database
        $allRoles = Role::pluck('name')->toArray();

        return response()->json([
            'user' => $model,
            'role' => $model->roles->pluck('name')->first(),
            'allRoles' => $allRoles
        ]);
    }

    public function update(Request $request, $type, $id)
    {
        $currentUser = auth()->guard('admin')->user();
        $currentRoles = $currentUser->roles->pluck('name')->toArray();
        $isAdminOnly = in_array('Admin', $currentRoles) && !in_array('SuperAdmin', $currentRoles);

        if ($type === 'admin') {
            $model = Admin::withTrashed()->findOrFail($id);
        } else {
            $model = User::withTrashed()->findOrFail($id);
        }

        if ($model->trashed() && !$currentUser->hasRole('SuperAdmin')) {
            abort(403, 'You cannot update a deleted user.');
        }

        $validated = $request->validate([
            'name' => ['required','string','max:255','regex:/^[A-Za-z0-9\s\-\_\.]+$/'],
            'email' => ['required','email'],
            'password' => ['nullable','string','min:8','confirmed'],
            'role' => ['required','string','exists:roles,name'],
        ]);

        // Restrict Admins from assigning SuperAdmin
        if ($isAdminOnly && $validated['role'] === 'SuperAdmin') {
            return response()->json([
                'errors' => ['role' => ['You are not allowed to assign SuperAdmin role.']]
            ], 422);
        }

        // If Admin, optionally prevent assigning another Admin if you want
        if ($isAdminOnly && $validated['role'] === 'Admin' && $model->id !== $currentUser->id) {
            return response()->json([
                'errors' => ['role' => ['Admins cannot assign Admin role to other users.']]
            ], 422);
        }

        // Update user
        $model->name = $validated['name'];
        $model->email = $validated['email'];
        if (!empty($validated['password'])) {
            $model->password = Hash::make($validated['password']);
        }
        $model->save();

        $model->syncRoles([$validated['role']]);

        return response()->json(['message' => 'User updated successfully']);
    }


    public function destroy($type, $id)
    {
        if ($type === 'admin') {
            $model = Admin::findOrFail($id);
        } else {
            $model = User::findOrFail($id);
        }

        $model->delete();
        return response()->json(['message'=>'User deleted successfully']);
    }

    public function forceDelete($type, $id)
    {
        $currentUser = auth()->user();

        if(!$currentUser->hasRole('SuperAdmin')){
            abort(403, 'Unauthorized');
        }

        $model = $type === 'admin' ? Admin::withTrashed()->findOrFail($id) : User::withTrashed()->findOrFail($id);

        // Prevent self deletion
        if ($model->id === $currentUser->id && $type === 'admin') {
            return response()->json(['errors' => ['self' => ['You cannot permanently delete your own account.']]], 422);
        }

        if(!$model->trashed()){
            return response()->json(['errors' => ['trashed' => ['User must be deleted first']]], 400);
        }

        $model->forceDelete();
        return response()->json(['message'=>'User permanently deleted']);
    }


    public function restore($type, $id)
    {
        // Only SuperAdmin can restore
        if (!Auth::guard('admin')->user()->hasRole('SuperAdmin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($type === 'admin') {
            $model = Admin::withTrashed()->findOrFail($id);
        } else {
            $model = User::withTrashed()->findOrFail($id);
        }

        $model->restore();

        return response()->json(['message' => 'User restored successfully']);
    }
}
