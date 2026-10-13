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

            // Jadwal Staff
            'jadwal-staff.index',
            'jadwal-staff.create',
            'jadwal-staff.show',
            'jadwal-staff.edit',
            'jadwal-staff.delete',
            'jadwal-staff.absen',
            'jadwal-staff.masuk',
            'jadwal-staff.pulang',

            // Program Pelatihan
            'programpelatihan.view',
            'programpelatihan.create',
            'programpelatihan.edit',
            'programpelatihan.delete',

            // Hari Libur
            'hari-libur.index',
            'hari-libur.show',
            'hari-libur.create',
            'hari-libur.edit',
            'hari-libur.delete',

            // Lokasi Absensi
            'lokasi-absensi.index',
            'lokasi-absensi.show',
            'lokasi-absensi.create',
            'lokasi-absensi.edit',
            'lokasi-absensi.delete',

            // Inventaris
            'inventaris.index',
            'inventaris.show',
            'inventaris.create',
            'inventaris.edit',
            'inventaris.delete',

            // Kategori Inventaris
            'kategori-inventaris.index',
            'kategori-inventaris.show',
            'kategori-inventaris.create',
            'kategori-inventaris.edit',
            'kategori-inventaris.delete',

            // LPJ Keuangan
            'lpj-keuangan.index',
            'lpj-keuangan.create',
            'lpj-keuangan.show',
            'lpj-keuangan.edit',
            'lpj-keuangan.delete',
            'lpj-keuangan.pengeluaran',
            'lpj-keuangan.ajukan',
            'lpj-keuangan.setujui',
            'lpj-keuangan.tolak',

            // Materi
            'materi.index',
            'materi.show',
            'materi.create',
            'materi.edit',
            'materi.delete',

            // Tugas
            'tugas.index',
            'tugas.show',
            'tugas.create',
            'tugas.edit',
            'tugas.delete',

            // Pengumpulan Tugas
            'pengumpulan-tugas.index',
            'pengumpulan-tugas.show',
            'pengumpulan-tugas.create',
            'pengumpulan-tugas.edit',
            'pengumpulan-tugas.delete',

            // Pengaturan Rekap Nilai
            'pengaturan-rekap-nilai.index',
            'pengaturan-rekap-nilai.edit',

            // Dashboard Peserta
            'peserta.dashboard.index',
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