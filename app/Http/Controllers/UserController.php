<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     */
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return Inertia::render('Users/Index', [
            'users' => $users,

            'roles' => $roles,

            'filters' => [
                'search' => $request->search,
                'per_page' => $request->integer('per_page', 10),
            ],
        ]);
    }

    /**
     * Menyimpan user baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'role' => [
                'required',
                'exists:roles,id',
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',

            'name.required' => 'Nama wajib diisi.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',

            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role yang dipilih tidak valid.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Buat User
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'username' => $validated['username'],
            'name' => $validated['name'],
            'password' => $validated['password'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assign Role
        |--------------------------------------------------------------------------
        */

        $role = Role::findById($validated['role']);

        $user->assignRole($role);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Mengupdate user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],

            'role' => [
                'required',
                'exists:roles,id',
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',

            'name.required' => 'Nama wajib diisi.',

            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',

            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role yang dipilih tidak valid.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update Data User
        |--------------------------------------------------------------------------
        */

        $data = [
            'username' => $validated['username'],
            'name' => $validated['name'],
        ];

        /*
        |--------------------------------------------------------------------------
        | Update Password jika diisi
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        /*
        |--------------------------------------------------------------------------
        | Update Role
        |--------------------------------------------------------------------------
        */

        $role = Role::findById($validated['role']);

        $user->syncRoles([$role]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus user.
     */
    public function destroy(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Jangan izinkan menghapus akun sendiri
        |--------------------------------------------------------------------------
        */

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}