<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Dashboard
            'dashboard.view',

            // User
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Role
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',

            // Permission
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',

            // Role Permissions
            'rolepermissions.view',
            'rolepermissions.create',
            'rolepermissions.edit',
            'rolepermissions.delete',

            // Pelatihan
            'pelatihan.view',
            'pelatihan.create',
            'pelatihan.edit',
            'pelatihan.delete',
            
            // Pendaftaran
            'pendaftaran.view',
            'pendaftaran.create',
            'pendaftaran.edit',
            'pendaftaran.delete',

            // Kelas
            'kelas.view',
            'kelas.create',
            'kelas.edit',
            'kelas.delete',

            // Tahap
            'tahap.view',
            'tahap.create',
            'tahap.edit',
            'tahap.delete',

            // Peserta
            'peserta.view',
            'peserta.create',
            'peserta.edit',
            'peserta.delete',

            // Pembayaran
            'pembayaran.view',
            'pembayaran.create',
            'pembayaran.edit',
            'pembayaran.delete',

            // Peserta Face
            'peserta-face.view',
            'peserta-face.create',
            'peserta-face.edit',
            'peserta-face.delete',

            // Absensi
            'absensi.view',
            'absensi.create',
            'absensi.edit',
            'absensi.delete',

            // Jadwal
            'jadwal.view',
            'jadwal.create',
            'jadwal.edit',
            'jadwal.delete',

            // Program Pelatihan
            'programpelatihan.view',
            'programpelatihan.create',
            'programpelatihan.edit',
            'programpelatihan.delete',
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // $petugas = Role::firstOrCreate([
        //     'name' => 'petugas',
        //     'guard_name' => 'web',
        // ]);

        // $anggota = Role::firstOrCreate([
        //     'name' => 'anggota',
            // 'guard_name' => 'web',
        // ]);

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        | Admin mendapatkan semua permission.
        */

        $admin->syncPermissions(Permission::all());

        /*
        |--------------------------------------------------------------------------
        | Petugas
        |--------------------------------------------------------------------------
        */

        // $petugas->syncPermissions([

        //     // Dashboard
        //     'dashboard.view',

        //     // Pelatihan
        //     'pelatihan.view',
        //     'pelatihan.create',
        //     'pelatihan.edit',
        //     'pelatihan.delete',

        //     // Kelas
        //     'kelas.view',
        //     'kelas.create',
        //     'kelas.edit',
        //     'kelas.delete',

        //     // Tahap
        //     'tahap.view',
        //     'tahap.create',
        //     'tahap.edit',
        //     'tahap.delete',

        //     // Peserta
        //     'peserta.view',
        //     'peserta.create',
        //     'peserta.edit',
        //     'peserta.delete',

        //     // Pembayaran
        //     'pembayaran.view',
        //     'pembayaran.create',
        //     'pembayaran.edit',
        //     'pembayaran.delete',

        //     // Peserta Face
        //     'peserta-face.view',
        //     'peserta-face.create',
        //     'peserta-face.edit',
        //     'peserta-face.delete',

        //     // Absensi
        //     'absensi.view',
        //     'absensi.create',
        //     'absensi.edit',
        //     'absensi.delete',
        // ]);

        /*
        |--------------------------------------------------------------------------
        | Anggota
        |--------------------------------------------------------------------------
        */

        // $anggota->syncPermissions([
        //     'dashboard.view',

        //     // Pelatihan
        //     'pelatihan.view',

        //     // Kelas
        //     'kelas.view',

        //     // Tahap
        //     'tahap.view',

        //     // Peserta
        //     'peserta.view',

        //     // Pembayaran
        //     'pembayaran.view',

        //     // Absensi
        //     'absensi.view',
        // ]);

        /*
        |--------------------------------------------------------------------------
        | Clear Cache
        |--------------------------------------------------------------------------
        */

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}