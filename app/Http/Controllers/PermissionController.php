<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
// use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
// use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        // Gunakan 'perPage' sesuai dengan frontend
        $perPage = $request->integer('perPage', 10);

        // Batasi pilihan per halaman
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $permissions = Permission::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('guard_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Permission/Index', [
            'permissions' => $permissions,

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
        return Inertia::render('Permission/Create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'actions' => ['required', 'array', 'min:1'],
            'actions.*' => [
                'required',
                'string',
                'in:view,index,create,edit,show,delete',
            ],
            'guard_name' => ['required', 'string'],
        ]);

        foreach ($validated['actions'] as $action) {
            Permission::firstOrCreate([
                'name' => $validated['name'] . '.' . $action,
                'guard_name' => $validated['guard_name'],
            ]);
        }

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil dibuat.');
    }

    /**
     * EDIT
     */
    public function edit(Permission $permission)
    {
        return Inertia::render('Permission/Edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * UPDATE
     */
    public function update(
        Request $request,
        Permission $permission
    ) {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('permissions', 'name')
                        ->where('guard_name', $request->input('guard_name'))
                        ->ignore($permission->id),
                ],

                'guard_name' => [
                    'required',
                    'string',
                    Rule::in(['web', 'peserta']),
                ],
            ],
            [
                'name.required' => 'Nama permission wajib diisi.',
                'name.unique' => 'Nama permission sudah digunakan.',
                'guard_name.required' => 'Guard name wajib dipilih.',
                'guard_name.in' => 'Guard name tidak valid.',
            ]
        );

        $permission->update([
            'name' => trim($validated['name']),
            'guard_name' => $validated['guard_name'],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil diperbarui.');
    }

    /**
     * DELETE
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil dihapus.');
    }
}