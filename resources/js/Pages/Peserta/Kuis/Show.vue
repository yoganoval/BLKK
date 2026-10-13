
<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    kuis: { type: Object, required: true },
})

const form = useForm({ jawaban: {} })

function submit() {
    form.post(route('peserta.kuis.jawaban.store', props.kuis.id))
}
</script>

<template>
    <main class="page">
        <Link :href="route('peserta.kuis.index')">← Kembali ke kuis</Link>

        <h1>{{ kuis.judul || kuis.nama || kuis.nama_kuis || 'Kuis' }}</h1>
        <p>{{ kuis.deskripsi || '' }}</p>

        <form @submit.prevent="submit">
            <section v-for="(soal, index) in (kuis.soal || [])" :key="soal.id" class="card">
                <h3>{{ index + 1 }}. {{ soal.pertanyaan || soal.soal || soal.teks_soal }}</h3>

                <div v-if="Array.isArray(soal.opsi)">
                    <label v-for="(opsi, i) in soal.opsi" :key="i" class="option">
                        <input
                            type="radio"
                            :name="`soal-${soal.id}`"
                            :value="opsi"
                            v-model="form.jawaban[soal.id]"
                        />
                        {{ typeof opsi === 'object' ? opsi.label : opsi }}
                    </label>
                </div>

                <textarea
                    v-else
                    v-model="form.jawaban[soal.id]"
                    rows="3"
                    placeholder="Tulis jawaban..."
                ></textarea>
            </section>

            <p v-if="!kuis.soal?.length">Soal kuis belum tersedia.</p>

            <button type="submit" :disabled="form.processing || !kuis.soal?.length">
                {{ form.processing ? 'Menyimpan...' : 'Kirim Jawaban' }}
            </button>
        </form>
    </main>
</template>

<style scoped>
.page { max-width: 850px; margin: 30px auto; padding: 20px; font-family: Arial, sans-serif; }
h1 { margin: 20px 0; }
.card { border: 1px solid #ddd; border-radius: 8px; padding: 18px; margin: 18px 0; }
.option { display: block; margin: 12px 0; }
textarea { display: block; box-sizing: border-box; width: 100%; padding: 10px; }
button { padding: 10px 16px; cursor: pointer; }
</style>
