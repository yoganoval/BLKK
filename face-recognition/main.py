import os
import json
import requests
import tempfile

from fastapi import FastAPI, UploadFile, File, Form, HTTPException
from deepface import DeepFace
from dotenv import load_dotenv


load_dotenv()

app = FastAPI(
    title="BLKK Face Recognition Service",
    version="1.0.0"
)


MODEL_NAME = "Facenet512"


@app.get("/")
def root():
    return {
        "status": "ok",
        "service": "BLKK Face Recognition",
        "model": MODEL_NAME
    }


@app.get("/health")
def health():
    return {
        "status": "healthy"
    }


@app.post("/embedding")
async def create_embedding(
    image: UploadFile = File(...)
):
    temp_path = None

    try:
        # Pastikan file gambar
        if not image.content_type:
            raise HTTPException(
                status_code=400,
                detail="File gambar tidak valid."
            )

        if not image.content_type.startswith("image/"):
            raise HTTPException(
                status_code=400,
                detail="File harus berupa gambar."
            )

        # Baca file
        contents = await image.read()

        if not contents:
            raise HTTPException(
                status_code=400,
                detail="File gambar kosong."
            )

        # Simpan sementara
        suffix = os.path.splitext(image.filename or ".jpg")[1]

        with tempfile.NamedTemporaryFile(
            delete=False,
            suffix=suffix
        ) as temp_file:

            temp_file.write(contents)
            temp_path = temp_file.name

        # Generate embedding
        result = DeepFace.represent(
            img_path=temp_path,
            model_name=MODEL_NAME,
            enforce_detection=True
        )

        if not result:
            raise HTTPException(
                status_code=422,
                detail="Wajah tidak ditemukan."
            )

        # Kalau hanya satu wajah
        face = result[0]

        embedding = face.get("embedding")

        if not embedding:
            raise HTTPException(
                status_code=422,
                detail="Embedding wajah gagal dibuat."
            )

        return {
            "success": True,
            "model": MODEL_NAME,
            "embedding": embedding,
            "embedding_dimension": len(embedding)
        }

    except HTTPException:
        raise

    except Exception as e:
        raise HTTPException(
            status_code=422,
            detail=f"Gagal memproses wajah: {str(e)}"
        )

    finally:
        if temp_path and os.path.exists(temp_path):
            os.remove(temp_path)