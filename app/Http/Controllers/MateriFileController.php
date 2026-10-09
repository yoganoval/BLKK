<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\MateriFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriFileController extends Controller
{
    /**
     * Upload file ke materi.
     */
    public function store(
        Request $request,
        Materi $materi
    ): RedirectResponse {
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'max:51200',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,webp,mp4,webm,zip,rar',
            ],
        ]);

        $file = $validated['file'];

        $path = $file->store(
            'materi/' . $materi->id,
            'public'
        );

        $materi->files()->create([
            'nama_file' => $file->getClientOriginalName(),
            'file_path' => $path,
            'tipe_file' => $file->getClientMimeType(),
            'ukuran' => $file->getSize(),
        ]);

        return back()->with(
            'success',
            'File materi berhasil diupload.'
        );
    }

    /**
     * Hapus file materi.
     */
    public function destroy(
        MateriFile $materiFile
    ): RedirectResponse {
        if (
            $materiFile->file_path &&
            Storage::disk('public')->exists(
                $materiFile->file_path
            )
        ) {
            Storage::disk('public')->delete(
                $materiFile->file_path
            );
        }

        $materiFile->delete();

        return back()->with(
            'success',
            'File materi berhasil dihapus.'
        );
    }
}