
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    materi: { type: Object, required: true },
})
</script>

<template>
    <main class="page">
        <Link :href="route('peserta.materi.index')">← Kembali ke materi</Link>

        <article>
            <h1>{{ materi.judul }}</h1>
            <p><b>Pelatihan:</b> {{ materi.pelatihan?.nama || materi.pelatihan?.nama_pelatihan || '-' }}</p>
            <p><b>Kelas:</b> {{ materi.kelas?.nama || '-' }}</p>
            <p><b>Tahap:</b> {{ materi.tahap?.nama || '-' }}</p>

            <hr />

            <h2>Deskripsi</h2>
            <p>{{ materi.deskripsi || 'Tidak ada deskripsi.' }}</p>

            <h2>Isi Materi</h2>
            <div class="content">{{ materi.isi_materi || 'Belum ada isi materi.' }}</div>

            <h2>File Materi</h2>
            <p v-if="!materi.files?.length">Belum ada file.</p>
            <ul v-else>
                <li v-for="file in materi.files" :key="file.id">
                    <a :href="`/storage/${file.file_path}`" target="_blank">
                        {{ file.nama_file }}
                    </a>
                </li>
            </ul>
        </article>
    </main>
</template>

<style scoped>
.page { max-width: 900px; margin: 30px auto; padding: 20px; font-family: Arial, sans-serif; }
article { margin-top: 25px; padding: 25px; border: 1px solid #ddd; border-radius: 8px; }
h1 { font-size: 28px; }
h2 { margin-top: 25px; }
.content { white-space: pre-wrap; line-height: 1.7; }
</style>
