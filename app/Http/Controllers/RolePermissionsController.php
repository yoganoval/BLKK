<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionsController extends Controller
{
    /**
     * =========================================================
     * INDEX
     * =========================================================
     *
     * Menampilkan semua role
     */
    public function index()
    {
        $roles = Role::query()
            ->withCount('permissions')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('RolePermissions/Index', [
            'roles' => $roles,
        ]);
    }


    /**
     * =========================================================
     * EDIT
     * =========================================================
     *
     * Menampilkan daftar permission
     * berdasarkan role yang dipilih.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::query()
            ->orderBy('name', 'asc')
            ->get();

        $rolePermissions = $role
            ->permissions()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        return Inertia::render('RolePermissions/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
            ],
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }


    /**
     * =========================================================
     * UPDATE
     * =========================================================
     *
     * Menyimpan permission untuk role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        $role->syncPermissions(
            $validated['permissions'] ?? []
        );

        return redirect()
            ->route('rolepermissions.index')
            ->with(
                'success',
                "Permission role {$role->name} berhasil diperbarui."
            );
    }
}