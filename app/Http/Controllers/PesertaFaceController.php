<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\PesertaFace;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Throwable;

class PesertaFaceController extends Controller
{
    /**
     * Daftar data wajah.
     */
    public function index()
    {
        $pesertaFaces = PesertaFace::with([
            'peserta',
            'peserta.pendaftarans.programPelatihan.pelatihan',
            'peserta.pendaftarans.programPelatihan.tahap',
            'peserta.pendaftarans.kelas',
        ])
            ->orderBy('created_at', 'desc')
            ->get();
// dd($pesertaFaces->toArray());
        return Inertia::render('PesertaFaces/Index', [
            'pesertaFaces' => $pesertaFaces,
        ]);
    }

    

    /**
     * Form registrasi wajah.
     */
    public function create()
    {
        $pesertas = Peserta::with([
            'pesertaFace',
            'pendaftarans.programPelatihan.pelatihan',
            'pendaftarans.programPelatihan.tahap',
            'pendaftarans.kelas',
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('PesertaFaces/Create', [
            'pesertas' => $pesertas,
        ]);
    }

    /**
     * Simpan data wajah baru.
     *
     * Alur:
     * Foto → Python Face Recognition → Embedding → Database
     */
    public function store(
        Request $request,
        FaceRecognitionService $faceRecognitionService
    ) {
        $validated = $request->validate([
            'peserta_id' => [
                'required',
                'integer',
                'exists:peserta,id',
                'unique:peserta_faces,peserta_id',
            ],

            'photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'registered_at' => [
                'nullable',
                'date',
            ],
        ], [
            'peserta_id.required' =>
                'Peserta wajib dipilih.',

            'peserta_id.exists' =>
                'Peserta tidak ditemukan.',

            'peserta_id.unique' =>
                'Peserta tersebut sudah memiliki data wajah.',

            'photo.required' =>
                'Foto wajah wajib diambil.',

            'photo.image' =>
                'File harus berupa gambar.',

            'photo.mimes' =>
                'Format foto harus JPG, JPEG, PNG, atau WEBP.',

            'photo.max' =>
                'Ukuran foto maksimal 5 MB.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate Face Embedding
        |--------------------------------------------------------------------------
        |
        | Foto dikirim ke Python FastAPI:
        |
        | POST /embedding
        |
        | Python menggunakan DeepFace + Facenet512
        | dan mengembalikan embedding 512 dimensi.
        |
        */

        try {
            $embedding = $faceRecognitionService->generateEmbedding(
                $request->file('photo')
            );
        } catch (Throwable $e) {

            return back()
                ->withErrors([
                    'photo' => $e->getMessage(),
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi hasil embedding
        |--------------------------------------------------------------------------
        */

        if (
            !is_array($embedding) ||
            count($embedding) === 0
        ) {
            return back()
                ->withErrors([
                    'photo' =>
                        'Embedding wajah tidak berhasil dibuat.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan Facenet512 menghasilkan 512 nilai
        |--------------------------------------------------------------------------
        */

        if (count($embedding) !== 512) {
            return back()
                ->withErrors([
                    'photo' =>
                        'Dimensi embedding wajah tidak valid. ' .
                        'Diharapkan 512 nilai, tetapi menerima ' .
                        count($embedding) . ' nilai.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan Foto
        |--------------------------------------------------------------------------
        */

        $photoPath = $request
            ->file('photo')
            ->store('peserta-faces', 'public');

        /*
        |--------------------------------------------------------------------------
        | Simpan Data Wajah
        |--------------------------------------------------------------------------
        */

        PesertaFace::create([
            'peserta_id' => $validated['peserta_id'],

            'face_embedding' => json_encode(
                $embedding,
                JSON_UNESCAPED_SLASHES
            ),

            'photo_path' => $photoPath,

            'registered_at' =>
                $validated['registered_at'] ?? now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('peserta-faces.index')
            ->with(
                'success',
                'Data wajah peserta berhasil didaftarkan.'
            );
    }

    /**
     * Detail data wajah.
     */
    public function show(PesertaFace $peserta_face)
    {
        $peserta_face->load([
            'peserta',
            'peserta.pendaftarans.programPelatihan.pelatihan',
            'peserta.pendaftarans.programPelatihan.tahap',
            'peserta.pendaftarans.kelas',
        ]);

        return Inertia::render('PesertaFaces/Show', [
            'pesertaFace' => $peserta_face,
        ]);
    }

    /**
     * Form edit data wajah.
     */
    public function edit(PesertaFace $peserta_face)
    {
        $pesertas = Peserta::orderBy('nama')->get();

        return Inertia::render('PesertaFaces/Edit', [
            'pesertaFace' => $peserta_face,
            'pesertas' => $pesertas,
        ]);
    }

    /**
     * Update data wajah.
     *
     * Jika foto diganti:
     * Foto baru → Python → embedding baru → database.
     *
     * Jika foto tidak diganti:
     * embedding lama tetap digunakan.
     */
    public function update(
        Request $request,
        PesertaFace $peserta_face,
        FaceRecognitionService $faceRecognitionService
    ) {
        $validated = $request->validate([
            'peserta_id' => [
                'required',
                'integer',
                'exists:peserta,id',
                'unique:peserta_faces,peserta_id,' .
                    $peserta_face->id,
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'registered_at' => [
                'nullable',
                'date',
            ],
        ], [
            'peserta_id.required' =>
                'Peserta wajib dipilih.',

            'peserta_id.exists' =>
                'Peserta tidak ditemukan.',

            'peserta_id.unique' =>
                'Peserta tersebut sudah memiliki data wajah.',

            'photo.image' =>
                'File harus berupa gambar.',

            'photo.mimes' =>
                'Format foto harus JPG, JPEG, PNG, atau WEBP.',

            'photo.max' =>
                'Ukuran foto maksimal 5 MB.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Data yang akan diupdate
        |--------------------------------------------------------------------------
        */

        $data = [
            'peserta_id' => $validated['peserta_id'],

            'registered_at' =>
                $validated['registered_at']
                ?? $peserta_face->registered_at
                ?? now(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Jika ada foto baru
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photo')) {

            /*
            |--------------------------------------------------------------------------
            | Generate embedding baru melalui Python
            |--------------------------------------------------------------------------
            */

            try {
                $embedding =
                    $faceRecognitionService->generateEmbedding(
                        $request->file('photo')
                    );
            } catch (Throwable $e) {

                return back()
                    ->withErrors([
                        'photo' => $e->getMessage(),
                    ])
                    ->withInput();
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi embedding
            |--------------------------------------------------------------------------
            */

            if (
                !is_array($embedding) ||
                count($embedding) !== 512
            ) {
                return back()
                    ->withErrors([
                        'photo' =>
                            'Embedding wajah tidak valid. ' .
                            'Facenet512 harus menghasilkan 512 nilai.',
                    ])
                    ->withInput();
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan foto baru
            |--------------------------------------------------------------------------
            */

            $newPhotoPath = $request
                ->file('photo')
                ->store('peserta-faces', 'public');

            /*
            |--------------------------------------------------------------------------
            | Hapus foto lama
            |--------------------------------------------------------------------------
            */

            if ($peserta_face->photo_path) {
                Storage::disk('public')->delete(
                    $peserta_face->photo_path
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Masukkan foto + embedding baru
            |--------------------------------------------------------------------------
            */

            $data['photo_path'] = $newPhotoPath;

            $data['face_embedding'] = json_encode(
                $embedding,
                JSON_UNESCAPED_SLASHES
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Database
        |--------------------------------------------------------------------------
        */

        $peserta_face->update($data);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('peserta-faces.index')
            ->with(
                'success',
                'Data wajah berhasil diperbarui.'
            );
    }






    /**
     * Hapus data wajah.
     */
    public function destroy(PesertaFace $peserta_face)
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus foto
        |--------------------------------------------------------------------------
        */

        if ($peserta_face->photo_path) {
            Storage::disk('public')->delete(
                $peserta_face->photo_path
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus data database
        |--------------------------------------------------------------------------
        */

        $peserta_face->delete();

        return redirect()
            ->route('peserta-faces.index')
            ->with(
                'success',
                'Data wajah berhasil dihapus.'
            );
    }
}