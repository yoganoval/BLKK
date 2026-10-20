<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\KomponenNilaiController;
use App\Http\Controllers\NilaiPesertaController;
use App\Http\Controllers\PesertaTugasController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\PengumpulanTugasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\MateriFileController;
use App\Http\Controllers\LpjKeuanganController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\KategoriInventarisController;
use App\Http\Controllers\HariLiburController;
use App\Http\Controllers\LokasiAbsensiController;
use App\Http\Controllers\AbsensiStaffController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JadwalStaffController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\PesertaFaceController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ProgramPelatihanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionsController;
use App\Http\Controllers\TahapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserFaceController;
use App\Http\Controllers\PengaturanRekapNilaiController;
use App\Http\Controllers\KuisController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\OpsiJawabanController;
use App\Http\Controllers\KoreksiEsaiController;


use App\Http\Controllers\Peserta\PesertaDashboardController;
use App\Http\Controllers\Peserta\PesertaMateriController;
use App\Http\Controllers\Peserta\PesertaTugasController as PesertaLmsTugasController;
use App\Http\Controllers\Peserta\PesertaPengumpulanTugasController as PesertaLmsPengumpulanTugasController;
use App\Http\Controllers\Peserta\PesertaKuisController;
use App\Http\Controllers\Peserta\PesertaJawabanKuisController;
use App\Http\Controllers\Peserta\PesertaProgresBelajarController;
use App\Http\Controllers\Peserta\PesertaSertifikatController;


////////// PESERTA //////////

// Dashboard dan logout peserta
Route::middleware('auth:peserta')->group(function () {
    Route::get('/peserta/dashboard', [
        PesertaDashboardController::class,
        'index',
    ])
        ->middleware('permission:peserta.dashboard.index,peserta')
        ->name('peserta.dashboard');

    Route::post('/peserta/logout', [
        AuthenticatedSessionController::class,
        'destroy',
    ])->name('peserta.logout');
});


// Fitur LMS peserta
Route::prefix('peserta')
    ->name('peserta.')
    ->middleware('auth:peserta')
    ->group(function () {

        // MATERI
        Route::get('/materi', [
            PesertaMateriController::class,
            'index',
        ])->name('materi.index');

        Route::get('/materi/{materi}', [
            PesertaMateriController::class,
            'show',
        ])->name('materi.show');


        // TUGAS
         Route::get('/tugas', [
            PesertaLmsTugasController::class,
            'index'
        ])->name('tugas.index');

        Route::get('/tugas/{tugas}', [
            PesertaLmsTugasController::class,
            'show'
        ])->name('tugas.show');

        // PENGUMPULAN TUGAS
        Route::post('/tugas/{tugas}/kumpulkan', [
            PesertaLmsPengumpulanTugasController::class,
            'store',
        ])->name('tugas.store');


        // KUIS
        Route::get('/kuis', [
            PesertaKuisController::class,
            'index',
        ])->name('kuis.index');

        Route::get('/kuis/{kuis}', [
            PesertaKuisController::class,
            'show',
        ])->name('kuis.show');

        Route::post('/kuis/{kuis}/jawaban', [
            PesertaJawabanKuisController::class,
            'store',
        ])->name('kuis.jawaban.store');

        Route::post(
            '/kuis/{kuis}/mulai',
            [PesertaKuisController::class, 'start']
        )->name('kuis.start');

        Route::get(
            '/percobaan-kuis/{percobaanKuis}',
            [PesertaKuisController::class, 'attempt']
        )->name('kuis.attempt');

        Route::put(
            '/percobaan-kuis/{percobaanKuis}/jawaban',
            [PesertaKuisController::class, 'saveAnswer']
        )->name('kuis.answer');

        Route::post(
            '/percobaan-kuis/{percobaanKuis}/selesai',
            [PesertaKuisController::class, 'submit']
        )->name('kuis.submit');

        Route::get(
            '/percobaan-kuis/{percobaanKuis}/hasil',
            [PesertaKuisController::class, 'result']
        )->name('kuis.result');

        
        // PROGRES BELAJAR
        Route::get('/progres', [
            PesertaProgresBelajarController::class,
            'index',
        ])->name('progres.index');

        // RIWAYAT PENGUMPULAN TUGAS
        Route::get('/pengumpulan-tugas', [
            PesertaLmsPengumpulanTugasController::class,
            'index',
        ])->name('pengumpulan.index');

        // SERTIFIKAT
        Route::get('/sertifikat', [
            PesertaSertifikatController::class,
            'index',
        ])->name('sertifikat.index');

        Route::get('/sertifikat/{sertifikat}/download', [
            PesertaSertifikatController::class,
            'download',
        ])->name('sertifikat.download');

    });




/*
|--------------------------------------------------------------------------
| GUEST
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/', function () {
        return redirect()->route('login');
    })->name('home');

    Route::get('/login', [
        AuthenticatedSessionController::class,
        'create'
    ])->name('login');

    Route::post('/login', [
        AuthenticatedSessionController::class,
        'store'
    ])->name('login.store');

});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [
        AuthenticatedSessionController::class,
        'destroy'
    ])->name('logout');


    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Pelatihan
    |--------------------------------------------------------------------------
    */

    Route::get('/pelatihans', [
        PelatihanController::class,
        'index'
    ])
        ->middleware('permission:pelatihan.view')
        ->name('pelatihans.index');

    Route::get('/pelatihans/create', [
        PelatihanController::class,
        'create'
    ])
        ->middleware('permission:pelatihan.create')
        ->name('pelatihans.create');

    Route::post('/pelatihans', [
        PelatihanController::class,
        'store'
    ])
        ->middleware('permission:pelatihan.create')
        ->name('pelatihans.store');

    Route::get('/pelatihans/{pelatihan}', [
        PelatihanController::class,
        'show'
    ])
        ->middleware('permission:pelatihan.view')
        ->name('pelatihans.show');

    Route::get('/pelatihans/{pelatihan}/edit', [
        PelatihanController::class,
        'edit'
    ])
        ->middleware('permission:pelatihan.edit')
        ->name('pelatihans.edit');

    Route::put('/pelatihans/{pelatihan}', [
        PelatihanController::class,
        'update'
    ])
        ->middleware('permission:pelatihan.edit')
        ->name('pelatihans.update');

    Route::delete('/pelatihans/{pelatihan}', [
        PelatihanController::class,
        'destroy'
    ])
        ->middleware('permission:pelatihan.delete')
        ->name('pelatihans.destroy');


    /*
    |--------------------------------------------------------------------------
    | Kelas
    |--------------------------------------------------------------------------
    */

    Route::get('/kelas', [
        KelasController::class,
        'index'
    ])
        ->middleware('permission:kelas.view')
        ->name('kelas.index');

    Route::get('/kelas/create', [
        KelasController::class,
        'create'
    ])
        ->middleware('permission:kelas.create')
        ->name('kelas.create');

    Route::post('/kelas', [
        KelasController::class,
        'store'
    ])
        ->middleware('permission:kelas.create')
        ->name('kelas.store');

    Route::get('/kelas/{kelas}', [
        KelasController::class,
        'show'
    ])
        ->middleware('permission:kelas.view')
        ->name('kelas.show');

    Route::get('/kelas/{kelas}/edit', [
        KelasController::class,
        'edit'
    ])
        ->middleware('permission:kelas.edit')
        ->name('kelas.edit');

    Route::put('/kelas/{kelas}', [
        KelasController::class,
        'update'
    ])
        ->middleware('permission:kelas.edit')
        ->name('kelas.update');

    Route::delete('/kelas/{kelas}', [
        KelasController::class,
        'destroy'
    ])
        ->middleware('permission:kelas.delete')
        ->name('kelas.destroy');


    /*
    |--------------------------------------------------------------------------
    | Tahap
    |--------------------------------------------------------------------------
    */

    Route::get('/tahaps', [
        TahapController::class,
        'index'
    ])
        ->middleware('permission:tahap.view')
        ->name('tahaps.index');

    Route::get('/tahaps/create', [
        TahapController::class,
        'create'
    ])
        ->middleware('permission:tahap.create')
        ->name('tahaps.create');

    Route::post('/tahaps', [
        TahapController::class,
        'store'
    ])
        ->middleware('permission:tahap.create')
        ->name('tahaps.store');

    Route::get('/tahaps/{tahap}', [
        TahapController::class,
        'show'
    ])
        ->middleware('permission:tahap.view')
        ->name('tahaps.show');

    Route::get('/tahaps/{tahap}/edit', [
        TahapController::class,
        'edit'
    ])
        ->middleware('permission:tahap.edit')
        ->name('tahaps.edit');

    Route::put('/tahaps/{tahap}', [
        TahapController::class,
        'update'
    ])
        ->middleware('permission:tahap.edit')
        ->name('tahaps.update');

    Route::delete('/tahaps/{tahap}', [
        TahapController::class,
        'destroy'
    ])
        ->middleware('permission:tahap.delete')
        ->name('tahaps.destroy');


    /*
    |--------------------------------------------------------------------------
    | Program Pelatihan
    |--------------------------------------------------------------------------
    |
    | program_pelatihan
    | - pelatihan_id
    | - tahap_id
    | - nama
    | - kuota
    | - status
    |
    */

    Route::get('/program-pelatihans', [
        ProgramPelatihanController::class,
        'index'
    ])
        ->middleware('permission:programpelatihan.view')
        ->name('program-pelatihans.index');

    Route::get('/program-pelatihans/create', [
        ProgramPelatihanController::class,
        'create'
    ])
        ->middleware('permission:programpelatihan.create')
        ->name('program-pelatihans.create');

    Route::post('/program-pelatihans', [
        ProgramPelatihanController::class,
        'store'
    ])
        ->middleware('permission:programpelatihan.create')
        ->name('program-pelatihans.store');

    Route::get('/program-pelatihans/{programPelatihan}', [
        ProgramPelatihanController::class,
        'show'
    ])
        ->middleware('permission:programpelatihan.view')
        ->name('program-pelatihans.show');

    Route::get('/program-pelatihans/{programPelatihan}/edit', [
        ProgramPelatihanController::class,
        'edit'
    ])
        ->middleware('permission:programpelatihan.edit')
        ->name('program-pelatihans.edit');

    Route::put('/program-pelatihans/{programPelatihan}', [
        ProgramPelatihanController::class,
        'update'
    ])
        ->middleware('permission:programpelatihan.edit')
        ->name('program-pelatihans.update');

    Route::delete('/program-pelatihans/{programPelatihan}', [
        ProgramPelatihanController::class,
        'destroy'
    ])
        ->middleware('permission:programpelatihan.delete')
        ->name('program-pelatihans.destroy');


    /*
    |--------------------------------------------------------------------------
    | Peserta
    |--------------------------------------------------------------------------
    */

    Route::get('/pesertas', [
        PesertaController::class,
        'index'
    ])
        ->middleware('permission:peserta.view')
        ->name('pesertas.index');

    Route::get('/pesertas/create', [
        PesertaController::class,
        'create'
    ])
        ->middleware('permission:peserta.create')
        ->name('pesertas.create');

    Route::post('/pesertas', [
        PesertaController::class,
        'store'
    ])
        ->middleware('permission:peserta.create')
        ->name('pesertas.store');

    Route::get('/pesertas/{peserta}', [
        PesertaController::class,
        'show'
    ])
        ->middleware('permission:peserta.view')
        ->name('pesertas.show');

    Route::get('/pesertas/{peserta}/edit', [
        PesertaController::class,
        'edit'
    ])
        ->middleware('permission:peserta.edit')
        ->name('pesertas.edit');

    Route::put('/pesertas/{peserta}', [
        PesertaController::class,
        'update'
    ])
        ->middleware('permission:peserta.edit')
        ->name('pesertas.update');

    Route::delete('/pesertas/{peserta}', [
        PesertaController::class,
        'destroy'
    ])
        ->middleware('permission:peserta.delete')
        ->name('pesertas.destroy');


    /*
    |--------------------------------------------------------------------------
    | Pendaftaran
    |--------------------------------------------------------------------------
    |
    | pendaftaran
    | - peserta_id
    | - program_pelatihan_id
    | - kelas_id
    | - tanggal_daftar
    | - status
    |
    */

    Route::get('/pendaftarans', [
        PendaftaranController::class,
        'index'
    ])
        ->middleware('permission:pendaftaran.view')
        ->name('pendaftarans.index');

    Route::get('/pendaftarans/create', [
        PendaftaranController::class,
        'create'
    ])
        ->middleware('permission:pendaftaran.create')
        ->name('pendaftarans.create');

    Route::post('/pendaftarans', [
        PendaftaranController::class,
        'store'
    ])
        ->middleware('permission:pendaftaran.create')
        ->name('pendaftarans.store');

    Route::get('/pendaftarans/{pendaftaran}', [
        PendaftaranController::class,
        'show'
    ])
        ->middleware('permission:pendaftaran.view')
        ->name('pendaftarans.show');

    Route::get('/pendaftarans/{pendaftaran}/edit', [
        PendaftaranController::class,
        'edit'
    ])
        ->middleware('permission:pendaftaran.edit')
        ->name('pendaftarans.edit');

    Route::put('/pendaftarans/{pendaftaran}', [
        PendaftaranController::class,
        'update'
    ])
        ->middleware('permission:pendaftaran.edit')
        ->name('pendaftarans.update');

    Route::delete('/pendaftarans/{pendaftaran}', [
        PendaftaranController::class,
        'destroy'
    ])
        ->middleware('permission:pendaftaran.delete')
        ->name('pendaftarans.destroy');


    /*
    |--------------------------------------------------------------------------
    | Pembayaran
    |--------------------------------------------------------------------------
    */

    Route::get('/pembayarans', [
        PembayaranController::class,
        'index'
    ])
        ->middleware('permission:pembayaran.view')
        ->name('pembayarans.index');

    Route::get('/pembayarans/create', [
        PembayaranController::class,
        'create'
    ])
        ->middleware('permission:pembayaran.create')
        ->name('pembayarans.create');

    Route::post('/pembayarans', [
        PembayaranController::class,
        'store'
    ])
        ->middleware('permission:pembayaran.create')
        ->name('pembayarans.store');

    Route::get('/pembayarans/{pembayaran}', [
        PembayaranController::class,
        'show'
    ])
        ->middleware('permission:pembayaran.view')
        ->name('pembayarans.show');

    Route::get('/pembayarans/{pembayaran}/edit', [
        PembayaranController::class,
        'edit'
    ])
        ->middleware('permission:pembayaran.edit')
        ->name('pembayarans.edit');

    Route::put('/pembayarans/{pembayaran}', [
        PembayaranController::class,
        'update'
    ])
        ->middleware('permission:pembayaran.edit')
        ->name('pembayarans.update');

    Route::delete('/pembayarans/{pembayaran}', [
        PembayaranController::class,
        'destroy'
    ])
        ->middleware('permission:pembayaran.delete')
        ->name('pembayarans.destroy');


    /*
    |--------------------------------------------------------------------------
    | Jadwal Absensi
    |--------------------------------------------------------------------------
    |
    | jadwal
    | - program_pelatihan_id
    | - kelas_id
    | - pertemuan
    | - tanggal
    | - jam_mulai
    | - jam_selesai
    | - kegiatan
    | - keterangan
    | - status
    |
    */


    Route::get('/jadwals', [
        JadwalController::class,
        'index'
    ])
        ->middleware('permission:jadwal.view')
        ->name('jadwals.index');

    Route::get('/jadwals/create', [
        JadwalController::class,
        'create'
    ])
        ->middleware('permission:jadwal.create')
        ->name('jadwals.create');

    Route::post('/jadwals', [
        JadwalController::class,
        'store'
    ])
        ->middleware('permission:jadwal.create')
        ->name('jadwals.store');

    /*
    |--------------------------------------------------------------------------
    | Detail Jadwal
    |--------------------------------------------------------------------------
    */

    Route::get('/jadwals/{jadwal}', [
        JadwalController::class,
        'show'
    ])
        ->middleware('permission:jadwal.view')
        ->name('jadwals.show');


    /*
    |--------------------------------------------------------------------------
    | Edit Jadwal
    |--------------------------------------------------------------------------
    */

    Route::get('/jadwals/{jadwal}/edit', [
        JadwalController::class,
        'edit'
    ])
        ->middleware('permission:jadwal.edit')
        ->name('jadwals.edit');

    /*
    |--------------------------------------------------------------------------
    | Update Jadwal
    |--------------------------------------------------------------------------
    */

    Route::put('/jadwals/{jadwal}', [
        JadwalController::class,
        'update'
    ])
        ->middleware('permission:jadwal.edit')
        ->name('jadwals.update');

    /*
    |--------------------------------------------------------------------------
    | Hapus Jadwal
    |--------------------------------------------------------------------------
    */

    Route::delete('/jadwals/{jadwal}', [
        JadwalController::class,
        'destroy'
    ])
        ->middleware('permission:jadwal.delete')
        ->name('jadwals.destroy');


    /*
    |--------------------------------------------------------------------------
    | Jadwal Staff
    |--------------------------------------------------------------------------
    */

    Route::get('/jadwal-staff', [
        JadwalStaffController::class,
        'index'
    ])
        ->middleware('permission:jadwal-staff.index')
        ->name('jadwal-staff.index');

    Route::get('/jadwal-staff/create', [
        JadwalStaffController::class,
        'create'
    ])
        ->middleware('permission:jadwal-staff.create')
        ->name('jadwal-staff.create');

    Route::post('/jadwal-staff', [
        JadwalStaffController::class,
        'store'
    ])
        ->middleware('permission:jadwal-staff.create')
        ->name('jadwal-staff.store');

    /*
    |--------------------------------------------------------------------------
    | Detail Jadwal Staff
    |--------------------------------------------------------------------------
    */

    Route::get('/jadwal-staff/{jadwal_staff}', [
        JadwalStaffController::class,
        'show'
    ])
        ->middleware('permission:jadwal-staff.show')
        ->name('jadwal-staff.show');

    /*
    |--------------------------------------------------------------------------
    | Edit Jadwal Staff
    |--------------------------------------------------------------------------
    */

    Route::get('/jadwal-staff/{jadwal_staff}/edit', [
        JadwalStaffController::class,
        'edit'
    ])
        ->middleware('permission:jadwal-staff.edit')
        ->name('jadwal-staff.edit');

    /*
    |--------------------------------------------------------------------------
    | Update Jadwal Staff
    |--------------------------------------------------------------------------
    */

    Route::put('/jadwal-staff/{jadwal_staff}', [
        JadwalStaffController::class,
        'update'
    ])
        ->middleware('permission:jadwal-staff.edit')
        ->name('jadwal-staff.update');

    /*
    |--------------------------------------------------------------------------
    | Hapus Jadwal Staff
    |--------------------------------------------------------------------------
    */

    Route::delete('/jadwal-staff/{jadwal_staff}', [
        JadwalStaffController::class,
        'destroy'
    ])
        ->middleware('permission:jadwal-staff.delete')
        ->name('jadwal-staff.destroy');


    Route::resource('hari-libur', HariLiburController::class)
        ->middleware([
            'index' => 'permission:hari-libur.index',
            'show' => 'permission:hari-libur.show',
            'create' => 'permission:hari-libur.create',
            'store' => 'permission:hari-libur.create',
            'edit' => 'permission:hari-libur.edit',
            'update' => 'permission:hari-libur.edit',
            'destroy' => 'permission:hari-libur.delete',
        ]);

    Route::resource(
        'lokasi-absensi',
        LokasiAbsensiController::class
    )
        ->middleware([
            'index' => 'permission:lokasi-absensi.index',
            'show' => 'permission:lokasi-absensi.show',
            'create' => 'permission:lokasi-absensi.create',
            'store' => 'permission:lokasi-absensi.create',
            'edit' => 'permission:lokasi-absensi.edit',
            'update' => 'permission:lokasi-absensi.edit',
            'destroy' => 'permission:lokasi-absensi.delete',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Absensi Staff
    |--------------------------------------------------------------------------
    */

    Route::get('/absensi-staff', [
        AbsensiStaffController::class,
        'index',
    ])
        ->middleware('permission:absensi-staff.index')
        ->name('absensi-staff.index');


    /*
    |--------------------------------------------------------------------------
    | Halaman Absen Staff
    |--------------------------------------------------------------------------
    */

    Route::get('/absensi-staff/absen', [
        AbsensiStaffController::class,
        'absen',
    ])
        ->middleware('permission:absensi-staff.absen')
        ->name('absensi-staff.absen');


    /*
    |--------------------------------------------------------------------------
    | Absen Masuk Staff
    |--------------------------------------------------------------------------
    */

    Route::post('/absensi-staff/masuk', [
        AbsensiStaffController::class,
        'masuk',
    ])
        ->middleware('permission:absensi-staff.masuk')
        ->name('absensi-staff.masuk');


    /*
    |--------------------------------------------------------------------------
    | Absen Pulang Staff
    |--------------------------------------------------------------------------
    */

    Route::post('/absensi-staff/pulang', [
        AbsensiStaffController::class,
        'pulang',
    ])
        ->middleware('permission:absensi-staff.pulang')
        ->name('absensi-staff.pulang');


    /*
    |--------------------------------------------------------------------------
    | Detail Absensi Staff
    |--------------------------------------------------------------------------
    */

    Route::get('/absensi-staff/{absensiStaff}', [
        AbsensiStaffController::class,
        'show',
    ])
        ->middleware('permission:absensi-staff.show')
        ->name('absensi-staff.show');


    /*
    |--------------------------------------------------------------------------
    | Hapus Absensi Staff
    |--------------------------------------------------------------------------
    */

    Route::delete('/absensi-staff/{absensiStaff}', [
        AbsensiStaffController::class,
        'destroy',
    ])
        ->middleware('permission:absensi-staff.delete')
        ->name('absensi-staff.destroy');
    /*
    |--------------------------------------------------------------------------
    | Absensi
    |--------------------------------------------------------------------------
    */

    Route::get('/absensi', [
        AbsensiController::class,
        'index'
    ])
        ->middleware('permission:absensi.view')
        ->name('absensi.index');

    Route::post('/absensi/recognize', [
        AbsensiController::class,
        'recognize'
    ])
        ->middleware('permission:absensi.create')
        ->name('absensi.recognize');

    Route::get('/rekap/absensi', [
        AbsensiController::class,
        'rekap'
    ])
        ->middleware('permission:absensi.view')
        ->name('rekap.absensi');

    Route::delete('/rekap/absensi/{absensi}', [
        AbsensiController::class,
        'destroy'
    ])
        ->middleware('permission:absensi.delete')
        ->name('rekap.absensi.destroy');


    /*
    |--------------------------------------------------------------------------
    | Peserta Face
    |--------------------------------------------------------------------------
    */

    Route::get('/peserta-faces', [
        PesertaFaceController::class,
        'index'
    ])
        ->middleware('permission:peserta-face.view')
        ->name('peserta-faces.index');

    Route::get('/peserta-faces/create', [
        PesertaFaceController::class,
        'create'
    ])
        ->middleware('permission:peserta-face.create')
        ->name('peserta-faces.create');

    Route::post('/peserta-faces', [
        PesertaFaceController::class,
        'store'
    ])
        ->middleware('permission:peserta-face.create')
        ->name('peserta-faces.store');

    Route::get('/peserta-faces/{peserta_face}', [
        PesertaFaceController::class,
        'show'
    ])
        ->middleware('permission:peserta-face.view')
        ->name('peserta-faces.show');

    Route::get('/peserta-faces/{peserta_face}/edit', [
        PesertaFaceController::class,
        'edit'
    ])
        ->middleware('permission:peserta-face.edit')
        ->name('peserta-faces.edit');

    Route::put('/peserta-faces/{peserta_face}', [
        PesertaFaceController::class,
        'update'
    ])
        ->middleware('permission:peserta-face.edit')
        ->name('peserta-faces.update');

    Route::delete('/peserta-faces/{peserta_face}', [
        PesertaFaceController::class,
        'destroy'
    ])
        ->middleware('permission:peserta-face.delete')
        ->name('peserta-faces.destroy');


    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    Route::get('/users', [
        UserController::class,
        'index'
    ])
        ->middleware('permission:user.view')
        ->name('users.index');

    Route::get('/users/create', [
        UserController::class,
        'create'
    ])
        ->middleware('permission:user.create')
        ->name('users.create');

    Route::post('/users', [
        UserController::class,
        'store'
    ])
        ->middleware('permission:user.create')
        ->name('users.store');

    Route::get('/users/{user}/edit', [
        UserController::class,
        'edit'
    ])
        ->middleware('permission:user.edit')
        ->name('users.edit');

    Route::put('/users/{user}', [
        UserController::class,
        'update'
    ])
        ->middleware('permission:user.edit')
        ->name('users.update');

    Route::delete('/users/{user}', [
        UserController::class,
        'destroy'
    ])
        ->middleware('permission:user.delete')
        ->name('users.destroy');


    Route::get(
        '/users/{user}/face',
        [UserFaceController::class, 'create']
    )->middleware('permission:user.edit')
    ->name('users.face.create');

    Route::post(
        '/users/{user}/face',
        [UserFaceController::class, 'store']
    )->middleware('permission:user.edit')
    ->name('users.face.store');

    Route::delete(
        '/users/{user}/face',
        [UserFaceController::class, 'destroy']
    )->middleware('permission:user.edit')
    ->name('users.face.destroy');


    /*
    |--------------------------------------------------------------------------
    | Role
    |--------------------------------------------------------------------------
    */

    Route::get('/roles', [
        RoleController::class,
        'index'
    ])
        ->middleware('permission:role.view')
        ->name('roles.index');

    Route::get('/roles/create', [
        RoleController::class,
        'create'
    ])
        ->middleware('permission:role.create')
        ->name('roles.create');

    Route::post('/roles', [
        RoleController::class,
        'store'
    ])
        ->middleware('permission:role.create')
        ->name('roles.store');

    Route::get('/roles/{role}/edit', [
        RoleController::class,
        'edit'
    ])
        ->middleware('permission:role.edit')
        ->name('roles.edit');

    Route::put('/roles/{role}', [
        RoleController::class,
        'update'
    ])
        ->middleware('permission:role.edit')
        ->name('roles.update');

    Route::delete('/roles/{role}', [
        RoleController::class,
        'destroy'
    ])
        ->middleware('permission:role.delete')
        ->name('roles.destroy');


    /*
    |--------------------------------------------------------------------------
    | Permission
    |--------------------------------------------------------------------------
    */

    Route::get('/permissions', [
        PermissionController::class,
        'index'
    ])
        ->middleware('permission:permission.view')
        ->name('permissions.index');

    Route::get('/permissions/create', [
        PermissionController::class,
        'create'
    ])
        ->middleware('permission:permission.create')
        ->name('permissions.create');

    Route::post('/permissions', [
        PermissionController::class,
        'store'
    ])
        ->middleware('permission:permission.create')
        ->name('permissions.store');

    Route::get('/permissions/{permission}/edit', [
        PermissionController::class,
        'edit'
    ])
        ->middleware('permission:permission.edit')
        ->name('permissions.edit');

    Route::put('/permissions/{permission}', [
        PermissionController::class,
        'update'
    ])
        ->middleware('permission:permission.edit')
        ->name('permissions.update');

    Route::delete('/permissions/{permission}', [
        PermissionController::class,
        'destroy'
    ])
        ->middleware('permission:permission.delete')
        ->name('permissions.destroy');


    /*
    |--------------------------------------------------------------------------
    | Role Permissions
    |--------------------------------------------------------------------------
    */

    Route::get('/role-permissions', [RolePermissionsController::class, 'index'])
        ->middleware('permission:rolepermissions.view')
        ->name('rolepermissions.index');

    Route::get('/role-permissions/{role}/edit', [RolePermissionsController::class, 'edit'])
        ->middleware('permission:rolepermissions.edit')
        ->name('rolepermissions.edit');

    Route::put('/role-permissions/{role}', [RolePermissionsController::class, 'update'])
        ->middleware('permission:rolepermissions.edit')
        ->name('rolepermissions.update');


    Route::resource('inventaris', InventarisController::class)
        ->parameters([
            'inventaris' => 'inventaris',
        ])
        ->middleware([
            'index' => 'permission:inventaris.index',
            'show' => 'permission:inventaris.show',
            'create' => 'permission:inventaris.create',
            'store' => 'permission:inventaris.create',
            'edit' => 'permission:inventaris.edit',
            'update' => 'permission:inventaris.edit',
            'destroy' => 'permission:inventaris.delete',
        ]);

Route::get(
    '/inventaris/public/{barcode}',
    [InventarisController::class, 'showPublic']
)->name('inventaris.public');

Route::resource(
    'kategori-inventaris',
    KategoriInventarisController::class
)->middleware([
    'index' => 'permission:kategori-inventaris.index',
    'show' => 'permission:kategori-inventaris.show',
    'create' => 'permission:kategori-inventaris.create',
    'store' => 'permission:kategori-inventaris.create',
    'edit' => 'permission:kategori-inventaris.edit',
    'update' => 'permission:kategori-inventaris.edit',
    'destroy' => 'permission:kategori-inventaris.delete',
]);



/*
|--------------------------------------------------------------------------
| LPJ KEUANGAN
|--------------------------------------------------------------------------
*/

Route::get('/lpj-keuangan', [
    LpjKeuanganController::class,
    'index'
])
    ->middleware('permission:lpj-keuangan.index')
    ->name('lpj-keuangan.index');


Route::get('/lpj-keuangan/create', [
    LpjKeuanganController::class,
    'create'
])
    ->middleware('permission:lpj-keuangan.create')
    ->name('lpj-keuangan.create');


Route::post('/lpj-keuangan', [
    LpjKeuanganController::class,
    'store'
])
    ->middleware('permission:lpj-keuangan.create')
    ->name('lpj-keuangan.store');


Route::get('/lpj-keuangan/{lpjKeuangan}', [
    LpjKeuanganController::class,
    'show'
])
    ->middleware('permission:lpj-keuangan.show')
    ->name('lpj-keuangan.show');


Route::get('/lpj-keuangan/{lpjKeuangan}/edit', [
    LpjKeuanganController::class,
    'edit'
])
    ->middleware('permission:lpj-keuangan.edit')
    ->name('lpj-keuangan.edit');


Route::put('/lpj-keuangan/{lpjKeuangan}', [
    LpjKeuanganController::class,
    'update'
])
    ->middleware('permission:lpj-keuangan.edit')
    ->name('lpj-keuangan.update');


Route::delete('/lpj-keuangan/{lpjKeuangan}', [
    LpjKeuanganController::class,
    'destroy'
])
    ->middleware('permission:lpj-keuangan.delete')
    ->name('lpj-keuangan.destroy');



/*
|--------------------------------------------------------------------------
| PENGELUARAN LPJ
|--------------------------------------------------------------------------
*/

Route::post('/lpj-keuangan/{lpjKeuangan}/pengeluaran', [
    LpjKeuanganController::class,
    'storePengeluaran'
])
    ->middleware('permission:lpj-keuangan.pengeluaran')
    ->name('lpj-keuangan.pengeluaran.store');


Route::put('/lpj-pengeluaran/{pengeluaran}', [
    LpjKeuanganController::class,
    'updatePengeluaran'
])
    ->middleware('permission:lpj-keuangan.pengeluaran')
    ->name('lpj-keuangan.pengeluaran.update');


Route::delete('/lpj-pengeluaran/{pengeluaran}', [
    LpjKeuanganController::class,
    'destroyPengeluaran'
])
    ->middleware('permission:lpj-keuangan.pengeluaran')
    ->name('lpj-keuangan.pengeluaran.destroy');



/*
|--------------------------------------------------------------------------
| STATUS LPJ
|--------------------------------------------------------------------------
*/

Route::post('/lpj-keuangan/{lpjKeuangan}/ajukan', [
    LpjKeuanganController::class,
    'ajukan'
])
    ->middleware('permission:lpj-keuangan.ajukan')
    ->name('lpj-keuangan.ajukan');


Route::post('/lpj-keuangan/{lpjKeuangan}/setujui', [
    LpjKeuanganController::class,
    'setujui'
])
    ->middleware('permission:lpj-keuangan.setujui')
    ->name('lpj-keuangan.setujui');


Route::post('/lpj-keuangan/{lpjKeuangan}/tolak', [
    LpjKeuanganController::class,
    'tolak'
])
    ->middleware('permission:lpj-keuangan.tolak')
    ->name('lpj-keuangan.tolak');




Route::resource('materi', MateriController::class
)->middleware([
    'index' => 'permission:materi.index',
    'show' => 'permission:materi.show',
    'create' => 'permission:materi.create',
    'store' => 'permission:materi.create',
    'edit' => 'permission:materi.edit',
    'update' => 'permission:materi.edit',
    'destroy' => 'permission:materi.delete',
]);

Route::post(
    'materi/{materi}/files',
    [MateriFileController::class, 'store']
    )->name('materi.files.store');
    
    Route::delete(
    'materi-files/{materiFile}',
    [MateriFileController::class, 'destroy']
    )->name('materi.files.destroy');
    

    
    Route::resource('pengumpulan-tugas', PengumpulanTugasController::class)
    ->parameters([
    'pengumpulan-tugas' => 'pengumpulan_tugas',
    ])->middleware([
        'index' => 'permission:tugas.index',
        'show' => 'permission:tugas.show',
        'create' => 'permission:tugas.create',
        'store' => 'permission:tugas.create',
        'edit' => 'permission:tugas.edit',
        'update' => 'permission:tugas.edit',
        'destroy' => 'permission:tugas.delete',
    ]);
    
    Route::resource(
        'tugas',
        TugasController::class
        )->middleware([
            'index' => 'permission:tugas.index',
            'show' => 'permission:tugas.show',
            'create' => 'permission:tugas.create',
            'store' => 'permission:tugas.create',
            'edit' => 'permission:tugas.edit',
            'update' => 'permission:tugas.edit',
            'destroy' => 'permission:tugas.delete',
        ]);

        
    Route::resource(
        'tugas',
        TugasController::class
        )->middleware([
            'index' => 'permission:tugas.index',
            'show' => 'permission:tugas.show',
            'create' => 'permission:tugas.create',
            'store' => 'permission:tugas.create',
            'edit' => 'permission:tugas.edit',
            'update' => 'permission:tugas.edit',
            'destroy' => 'permission:tugas.delete',
        ]);

 // =========================
// MANAJEMEN KUIS
// =========================
Route::resource('kuis', KuisController::class)
    ->middleware([
        'index'   => 'permission:kuis.index',
        'show'    => 'permission:kuis.show',
        'create'  => 'permission:kuis.create',
        'store'   => 'permission:kuis.create',
        'edit'    => 'permission:kuis.edit',
        'update'  => 'permission:kuis.edit',
        'destroy' => 'permission:kuis.delete',
    ]);

    // =========================
    // DAFTAR SOAL GLOBAL
    // =========================
    Route::get('/soal-kuis', [SoalController::class, 'globalIndex'])
        ->middleware('permission:soal.index')
        ->name('soal.global.index');

    // Resource soal tetap bersarang
    Route::resource('kuis.soal', SoalController::class)
        ->middleware([
            'index'   => 'permission:soal.index',
            'show'    => 'permission:soal.show',
            'create'  => 'permission:soal.create',
            'store'   => 'permission:soal.create',
            'edit'    => 'permission:soal.edit',
            'update'  => 'permission:soal.edit',
            'destroy' => 'permission:soal.delete',
        ]);

    // =========================
    // DAFTAR OPSI JAWABAN GLOBAL
    // =========================
    Route::get('/opsi-jawaban-kuis', [
        OpsiJawabanController::class,
        'globalIndex',
    ])
        ->middleware('permission:opsi-jawaban.index')
        ->name('opsi-jawaban.global.index');

    // Resource opsi jawaban tetap bersarang
    Route::resource('soal.opsi-jawaban', OpsiJawabanController::class)
        ->middleware([
            'index'   => 'permission:opsi-jawaban.index',
            'show'    => 'permission:opsi-jawaban.show',
            'create'  => 'permission:opsi-jawaban.create',
            'store'   => 'permission:opsi-jawaban.create',
            'edit'    => 'permission:opsi-jawaban.edit',
            'update'  => 'permission:opsi-jawaban.edit',
            'destroy' => 'permission:opsi-jawaban.delete',
        ]);

    Route::get('/koreksi-esai', [KoreksiEsaiController::class, 'index'])
        ->name('koreksi-esai.index')
        ->middleware('permission:koreksi-esai.index');

    Route::get('/koreksi-esai/{percobaanKuis}', [KoreksiEsaiController::class, 'show'])
        ->name('koreksi-esai.show')
        ->middleware('permission:koreksi-esai.show');

    Route::put('/koreksi-esai/{percobaanKuis}', [KoreksiEsaiController::class, 'update'])
        ->name('koreksi-esai.update')
        ->middleware('permission:koreksi-esai.edit');

    Route::get(
        '/pengaturan-rekap-nilai',
        [PengaturanRekapNilaiController::class, 'index']
    )
        ->middleware('permission:pengaturan-rekap-nilai.index')
        ->name('pengaturan-rekap-nilai.index');

    Route::put(
        '/pengaturan-rekap-nilai/{kelas}',
        [PengaturanRekapNilaiController::class, 'update']
    )
        ->middleware('permission:pengaturan-rekap-nilai.edit')
        ->name('pengaturan-rekap-nilai.update');
    
    
    Route::resource(
        'komponen-nilai',
        KomponenNilaiController::class
    );

    Route::resource(
        'nilai-peserta',
        NilaiPesertaController::class
    );

    Route::get(
        '/penilaian',
        [PenilaianController::class, 'index']
    )->name('penilaian.index');

    Route::post(
        '/penilaian',
        [PenilaianController::class, 'store']
    )->name('penilaian.store');


});
        
        
        // require __DIR__.'/auth.php';
        