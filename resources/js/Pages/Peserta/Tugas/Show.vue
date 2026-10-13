
<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    tugas: { type: Object, required: true },
})

const form = useForm({
    jawaban: '',
    file: null,
})

function submit() {
    form.post(route('peserta.tugas.kumpulkan', props.tugas.id), {
        forceFormData: true,
    })
}
</script>

<template>
    <main class="page">
        <Link :href="route('peserta.tugas.index')">← Kembali ke tugas</Link>

        <h1>{{ tugas.judul || tugas.nama_tugas || tugas.nama || 'Detail Tugas' }}</h1>

        <section class="card">
            <h3>Petunjuk Tugas</h3>
            <p>{{ tugas.deskripsi || tugas.instruksi || 'Tidak ada deskripsi tugas.' }}</p>
            <p v-if="tugas.deadline"><b>Batas pengumpulan:</b> {{ tugas.deadline }}</p>
        </section>

        <form @submit.prevent="submit" class="card">
            <h2>Kumpulkan Tugas</h2>

            <label for="jawaban">Jawaban tertulis</label>
            <textarea id="jawaban" v-model="form.jawaban" rows="6"></textarea>
            <small v-if="form.errors.jawaban" class="error">{{ form.errors.jawaban }}</small>

            <label for="file">Lampiran (opsional)</label>
            <input
                id="file"
                type="file"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                @change="form.file = $event.target.files[0] || null"
            />
            <small v-if="form.errors.file" class="error">{{ form.errors.file }}</small>

            <button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Mengirim...' : 'Kirim Tugas' }}
            </button>

            <p v-if="form.recentlySuccessful">Tugas berhasil dikirim.</p>
        </form>
    </main>
</template>

<style scoped>
.page { max-width: 850px; margin: 30px auto; padding: 20px; font-family: Arial, sans-serif; }
h1 { margin: 20px 0; }
.card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin: 20px 0; }
label { display: block; margin: 15px 0 6px; }
textarea, input[type=file] { display: block; width: 100%; box-sizing: border-box; padding: 10px; }
textarea { border: 1px solid #aaa; }
button { margin-top: 18px; padding: 10px 16px; cursor: pointer; }
button:disabled { opacity: .6; cursor: not-allowed; }
.error { color: #c00; }
</style>
