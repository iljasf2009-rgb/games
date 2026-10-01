<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_management_pages_are_restricted_to_admins(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::create(['name' => 'klant', 'guard_name' => 'web']));

        $this->actingAs($customer)->get(route('admin.permissions.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.roles.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.role-permissions.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.user-roles.index'))->assertForbidden();
        $this->actingAs($customer)->post(route('admin.permissions.store'), ['name' => 'games.create'])->assertForbidden();
    }

    public function test_admin_can_manage_permissions_roles_and_assignments(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->assignRole($adminRole);
        $user = User::factory()->create();

        $this->actingAs($admin)->get(route('admin.permissions.index'))->assertOk();
        $this->get(route('admin.roles.index'))->assertOk();
        $this->get(route('admin.role-permissions.index'))->assertOk();
        $this->get(route('admin.user-roles.index'))->assertOk();

        $this->post(route('admin.permissions.store'), ['name' => 'games.create'])->assertRedirect(route('admin.permissions.index'));
        $permission = Permission::where('name', 'games.create')->firstOrFail();
        $this->assertSame('web', $permission->guard_name);
        $this->put(route('admin.permissions.update', $permission), ['name' => 'games.update'])->assertRedirect(route('admin.permissions.index'));
        $permission->refresh();
        $this->assertSame('games.update', $permission->name);
        $secondPermission = Permission::create(['name' => 'games.delete', 'guard_name' => 'web']);

        $this->post(route('admin.roles.store'), ['name' => 'editor'])->assertRedirect(route('admin.roles.index'));
        $role = Role::where('name', 'editor')->firstOrFail();
        $this->assertSame('web', $role->guard_name);
        $this->put(route('admin.roles.update', $role), ['name' => 'manager'])->assertRedirect(route('admin.roles.index'));
        $role->refresh();
        $this->assertSame('manager', $role->name);

        $this->post(route('admin.role-permissions.store'), [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ])->assertRedirect(route('admin.role-permissions.index'));
        $this->assertTrue($role->fresh()->hasPermissionTo($permission));
        $this->put(route('admin.role-permissions.update', [$role, $permission]), [
            'role_id' => $adminRole->id,
            'permission_id' => $secondPermission->id,
        ])->assertRedirect(route('admin.role-permissions.index'));
        $this->assertFalse($role->fresh()->hasPermissionTo($permission));
        $this->assertTrue($adminRole->fresh()->hasPermissionTo($secondPermission));
        $this->put(route('admin.role-permissions.update', [$adminRole, $secondPermission]), [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ])->assertRedirect(route('admin.role-permissions.index'));

        $this->post(route('admin.user-roles.store'), [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ])->assertRedirect(route('admin.user-roles.index'));
        $this->assertTrue($user->fresh()->hasRole($role));
        $this->put(route('admin.user-roles.update', [$user, $role]), [
            'user_id' => $admin->id,
            'role_id' => $adminRole->id,
        ])->assertRedirect(route('admin.user-roles.index'));
        $this->assertFalse($user->fresh()->hasRole($role));
        $this->assertTrue($admin->fresh()->hasRole($adminRole));
        $this->put(route('admin.user-roles.update', [$admin, $adminRole]), [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ])->assertRedirect(route('admin.user-roles.index'));

        $this->delete(route('admin.user-roles.destroy', [$user, $role]))->assertRedirect(route('admin.user-roles.index'));
        $this->assertFalse($user->fresh()->hasRole($role));
        $this->delete(route('admin.role-permissions.destroy', [$role, $permission]))->assertRedirect(route('admin.role-permissions.index'));
        $this->assertFalse($role->fresh()->hasPermissionTo($permission));

        $this->delete(route('admin.roles.destroy', $role))->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
        $this->delete(route('admin.permissions.destroy', $permission))->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }
}
