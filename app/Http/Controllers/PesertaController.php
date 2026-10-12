<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PesertaController extends Controller
{
    /**
     * Daftar dan pencarian akun peserta.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        $pesertas = Peserta::query()
            ->with(['pesertaFace'])
            ->withCount([
                'pendaftarans',
                'pendaftarans as pendaftaran_aktif_count' => function ($query) {
                    $query->where('status', 'aktif');
                },
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('kamar', 'like', "%{$search}%")
                        ->orWhere('lembaga', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

            $totalLembaga = Peserta::query()
                ->whereNotNull('lembaga')
                ->where('lembaga', '<>', '')
                ->distinct()
                ->count('lembaga');
                
            return Inertia::render('Peserta/Index', [
                'pesertas' => $pesertas,
                'filters' => [
                    'search' => $search,
                ],
                'totalLembaga' => $totalLembaga,
            ]);
    }

    /**
     * Form tambah akun peserta.
     */
    public function create()
    {
        return Inertia::render('Peserta/Create');
    }

    /**
     * Simpan akun peserta baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kamar' => ['nullable', 'string', 'max:100'],
            'lembaga' => ['nullable', 'string', 'max:255'],

            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'unique:peserta,username',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'is_active' => ['sometimes', 'boolean'],
        ], [
            'nama.required' => 'Nama peserta wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan peserta lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Peserta::create($validated);

        return redirect()
            ->route('pesertas.index')
            ->with('success', 'Akun peserta berhasil dibuat.');
    }

    /**
     * Detail akun dan seluruh pendaftaran pelatihan.
     */
    public function show(Peserta $peserta)
    {
        $peserta->load([
            'pesertaFace',
            'pendaftarans.programPelatihan.pelatihan',
            'pendaftarans.programPelatihan.tahap',
            'pendaftarans.kelas',
            'pendaftarans.pembayarans',
            'pendaftarans.absensis.jadwal',
        ]);

        return Inertia::render('Peserta/Show', [
            'peserta' => $peserta,
        ]);
    }

    /**
     * Form edit akun peserta.
     */
    public function edit(Peserta $peserta)
    {
        return Inertia::render('Peserta/Edit', [
            'peserta' => $peserta,
        ]);
    }

    /**
     * Perbarui data akun peserta.
     */
    public function update(Request $request, Peserta $peserta)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kamar' => ['nullable', 'string', 'max:100'],
            'lembaga' => ['nullable', 'string', 'max:255'],

            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('peserta', 'username')->ignore($peserta->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'is_active' => ['required', 'boolean'],
        ], [
            'nama.required' => 'Nama peserta wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan peserta lain.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $peserta->update($validated);

        return redirect()
            ->route('pesertas.index')
            ->with('success', 'Data akun peserta berhasil diperbarui.');
    }

    /**
     * Hapus akun peserta jika belum memiliki relasi.
     */
    public function destroy(Peserta $peserta)
    {
        if ($peserta->pendaftarans()->exists()) {
            return back()->with(
                'error',
                'Akun tidak dapat dihapus karena sudah memiliki data pendaftaran.'
            );
        }

        if ($peserta->pesertaFace()->exists()) {
            return back()->with(
                'error',
                'Akun tidak dapat dihapus karena masih memiliki data wajah.'
            );
        }

        $peserta->delete();

        return redirect()
            ->route('pesertas.index')
            ->with('success', 'Akun peserta berhasil dihapus.');
    }
}