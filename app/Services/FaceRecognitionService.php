<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FaceRecognitionService
{
    protected string $pythonUrl;

    public function __construct()
    {
        $this->pythonUrl = rtrim(
            config('services.face_recognition.url'),
            '/'
        );
    }

    public function generateEmbedding(
        UploadedFile $image
    ): array {
        $response = Http::timeout(60)
            ->attach(
                'image',
                file_get_contents($image->getRealPath()),
                $image->getClientOriginalName()
            )
            ->post($this->pythonUrl . '/embedding');

        if (!$response->successful()) {
            $message = $response->json('detail')
                ?? 'Gagal menghubungi Face Recognition Service.';

            throw new RuntimeException($message);
        }

        $data = $response->json();

        if (
            !isset($data['success']) ||
            !$data['success'] ||
            !isset($data['embedding'])
        ) {
            throw new RuntimeException(
                'Embedding wajah tidak valid.'
            );
        }

        return $data['embedding'];
    }
}