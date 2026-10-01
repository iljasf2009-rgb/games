<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessManagementController extends Controller
{
    public function permissions(): View
    {
        return view('admin.permissions.index', [
            'permissions' => Permission::where('guard_name', 'web')->with('roles')->orderBy('name')->get(),
        ]);
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->where('guard_name', 'web')],
        ]);

        Permission::create(['name' => $data['name'], 'guard_name' => 'web']);

        return to_route('admin.permissions.index')->with('success', 'Permissie aangemaakt.');
    }

    public function updatePermission(Request $request, Permission $permission): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->where('guard_name', 'web')->ignore($permission->id)],
        ]);

        $permission->update(['name' => $data['name']]);

        return to_route('admin.permissions.index')->with('success', 'Permissie gewijzigd.');
    }

    public function destroyPermission(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return to_route('admin.permissions.index')->with('success', 'Permissie verwijderd.');
    }

    public function roles(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::where('guard_name', 'web')->with('permissions', 'users')->orderBy('name')->get(),
        ]);
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->where('guard_name', 'web')],
        ]);

        Role::create(['name' => $data['name'], 'guard_name' => 'web']);

        return to_route('admin.roles.index')->with('success', 'Rol aangemaakt.');
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->where('guard_name', 'web')->ignore($role->id)],
        ]);

        $role->update(['name' => $data['name']]);

        return to_route('admin.roles.index')->with('success', 'Rol gewijzigd.');
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        $role->delete();

        return to_route('admin.roles.index')->with('success', 'Rol verwijderd.');
    }

    public function rolePermissions(): View
    {
        return view('admin.role-permissions.index', [
            'roles' => Role::where('guard_name', 'web')->with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::where('guard_name', 'web')->orderBy('name')->get(),
        ]);
    }

    public function storeRolePermission(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'permission_id' => ['required', 'integer', 'exists:permissions,id'],
        ]);

        Role::findOrFail($data['role_id'])->givePermissionTo(Permission::findOrFail($data['permission_id']));

        return to_route('admin.role-permissions.index')->with('success', 'Permissie aan rol gekoppeld.');
    }

    public function updateRolePermission(Request $request, Role $role, Permission $permission): RedirectResponse
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'permission_id' => ['required', 'integer', 'exists:permissions,id'],
        ]);

        DB::transaction(function () use ($role, $permission, $data): void {
            $role->revokePermissionTo($permission);
            Role::findOrFail($data['role_id'])->givePermissionTo(Permission::findOrFail($data['permission_id']));
        });

        return to_route('admin.role-permissions.index')->with('success', 'Koppeling gewijzigd.');
    }

    public function destroyRolePermission(Role $role, Permission $permission): RedirectResponse
    {
        $role->revokePermissionTo($permission);

        return to_route('admin.role-permissions.index')->with('success', 'Koppeling verwijderd.');
    }

    public function userRoles(): View
    {
        return view('admin.user-roles.index', [
            'users' => User::with('roles')->orderBy('name')->get(),
            'roles' => Role::where('guard_name', 'web')->orderBy('name')->get(),
        ]);
    }

    public function storeUserRole(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        User::findOrFail($data['user_id'])->assignRole(Role::findOrFail($data['role_id']));

        return to_route('admin.user-roles.index')->with('success', 'Rol aan gebruiker gekoppeld.');
    }

    public function updateUserRole(Request $request, User $user, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        DB::transaction(function () use ($user, $role, $data): void {
            $user->removeRole($role);
            $user->assignRole(Role::findOrFail($data['role_id']));
        });

        return to_route('admin.user-roles.index')->with('success', 'Koppeling gewijzigd.');
    }

    public function destroyUserRole(User $user, Role $role): RedirectResponse
    {
        $user->removeRole($role);

        return to_route('admin.user-roles.index')->with('success', 'Koppeling verwijderd.');
    }
}
