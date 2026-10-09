<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use RuntimeException;

class UserFaceController extends Controller
{
    public function create(User $user)
    {
        return Inertia::render('Users/Face', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'face_registered' => !empty($user->face_embedding),
                'face_photo_path' => $user->face_photo_path,
                'face_registered_at' => $user->face_registered_at,
            ],
        ]);
    }

    public function store(
        Request $request,
        User $user,
        FaceRecognitionService $faceRecognitionService
    ) {
        $request->validate([
            'photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        try {
            /*
             * Generate embedding dari Python
             */
            $embedding = $faceRecognitionService
                ->generateEmbedding(
                    $request->file('photo')
                );

            /*
             * Pastikan embedding berisi 512 nilai
             */
            if (!is_array($embedding) || count($embedding) !== 512) {
                throw new RuntimeException(
                    'Embedding wajah harus berisi 512 nilai.'
                );
            }

            /*
             * Hapus foto wajah lama jika ada
             */
            if ($user->face_photo_path) {
                Storage::disk('public')->delete(
                    $user->face_photo_path
                );
            }

            /*
             * Simpan foto baru
             */
            $photoPath = $request
                ->file('photo')
                ->store('faces/users', 'public');

            /*
             * Simpan data wajah
             */
            $user->update([
                'face_embedding' => json_encode($embedding),
                'face_photo_path' => $photoPath,
                'face_registered_at' => now(),
            ]);

            return redirect()
                ->route('users.index', $user)
                ->with(
                    'success',
                    'Wajah ' . $user->name . ' berhasil didaftarkan.'
                );

        } catch (RuntimeException $e) {

            return back()
                ->withErrors([
                    'photo' => $e->getMessage(),
                ])
                ->withInput();

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withErrors([
                    'photo' => 'Terjadi kesalahan saat memproses wajah.',
                ])
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        if ($user->face_photo_path) {
            Storage::disk('public')->delete(
                $user->face_photo_path
            );
        }

        $user->update([
            'face_embedding' => null,
            'face_photo_path' => null,
            'face_registered_at' => null,
        ]);

        return redirect()
            ->route('users.index', $user)
            ->with(
                'success',
                'Data wajah ' . $user->name . ' berhasil dihapus.'
            );
    }
}