<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        $perPage = $request->integer('perPage', 10);

        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $roles = Role::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Role/Index', [
            'roles' => $roles,
            'filters' => [
                'search' => $request->search ?? '',
                'perPage' => $perPage,
            ],
        ]);
    }

    /**
     * CREATE
     */
    public function create()
    {
        return Inertia::render('Role/Create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $request->input('guard_name', 'web')),
            ],
            'guard_name' => [
                'required',
                'string',
                Rule::in(['web', 'api', 'peserta']),
            ],
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan pada guard tersebut.',
            'guard_name.required' => 'Guard name wajib dipilih.',
            'guard_name.in' => 'Guard name harus web, api, atau peserta.',
        ]);

        Role::create([
            'name' => trim($validated['name']),
            'guard_name' => $validated['guard_name'],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * EDIT
     */
    public function edit(Role $role)
    {
        return Inertia::render('Role/Edit', [
            'role' => $role,
        ]);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $request->input('guard_name', $role->guard_name))
                    ->ignore($role->id),
            ],
            'guard_name' => [
                'required',
                'string',
                Rule::in(['web', 'api', 'peserta']),
            ],
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan pada guard tersebut.',
            'guard_name.required' => 'Guard name wajib dipilih.',
            'guard_name.in' => 'Guard name harus web, api, atau peserta.',
        ]);

        $role->update([
            'name' => trim($validated['name']),
            'guard_name' => $validated['guard_name'],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * DELETE
     */
    public function destroy(Role $role)
    {
        if ($role->name === 'super-admin') {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role super-admin tidak dapat dihapus.');
        }

        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}