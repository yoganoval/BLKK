<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        // Gunakan 'perPage' (sesuai dengan frontend)
        $perPage = $request->integer('perPage', 10);

        // Batasi pilihan per halaman
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $roles = Role::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->withQueryString(); // Penting: menyimpan query string di pagination

        return Inertia::render('Role/Index', [
            'roles' => $roles,
            'filters' => [
                'search' => $request->search ?? '',
                'perPage' => $perPage, // Konsisten: perPage bukan per_page
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
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:roles,name',
                ],
            ],
            [
                'name.required' => 'Nama role wajib diisi.',
                'name.unique' => 'Nama role sudah digunakan.',
            ]
        );

        Role::create([
            'name' => trim($validated['name']),
            'guard_name' => 'web',
        ]);

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
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:roles,name,' . $role->id,
                ],
            ],
            [
                'name.required' => 'Nama role wajib diisi.',
                'name.unique' => 'Nama role sudah digunakan.',
            ]
        );

        $role->update([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * DELETE
     */
    public function destroy(Role $role)
    {
        // Role super-admin tidak boleh dihapus
        if ($role->name === 'super-admin') {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role super-admin tidak dapat dihapus.');
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}