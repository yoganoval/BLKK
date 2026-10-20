<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    kuis: { type: Object, required: true },
})

const form = useForm({
    pertanyaan: '',
    tipe: 'pilihan_ganda',
    bobot: 1,
    urutan: 1,
})

function submit() {
    form.post(route('kuis.soal.store', props.kuis.id))
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 dark:bg-gray-950">
        <div class="mx-auto max-w-3xl">
            <Link :href="route('kuis.soal.index', kuis.id)" class="text-sm text-blue-600 hover:underline">
                ← Kembali ke daftar soal
            </Link>

            <h1 class="my-5 text-2xl font-bold dark:text-white">Tambah Soal</h1>
            <p class="mb-5 text-sm text-gray-500">Kuis: {{ kuis.judul }}</p>

            <form @submit.prevent="submit" class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <label class="mb-1 block text-sm font-medium">Pertanyaan</label>
                    <textarea v-model="form.pertanyaan" rows="5" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    <p v-if="form.errors.pertanyaan" class="mt-1 text-sm text-red-600">{{ form.errors.pertanyaan }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Tipe Soal</label>
                    <select v-model="form.tipe" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        <option value="pilihan_ganda">Pilihan Ganda</option>
                        <option value="benar_salah">Benar / Salah</option>
                    </select>
                    <p v-if="form.errors.tipe" class="mt-1 text-sm text-red-600">{{ form.errors.tipe }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Bobot Nilai</label>
                        <input v-model="form.bobot" type="number" min="0.01" max="999.99" step="0.01" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                        <p v-if="form.errors.bobot" class="mt-1 text-sm text-red-600">{{ form.errors.bobot }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Urutan Soal</label>
                        <input v-model="form.urutan" type="number" min="1" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                        <p v-if="form.errors.urutan" class="mt-1 text-sm text-red-600">{{ form.errors.urutan }}</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 pt-5 dark:border-gray-800">
                    <Link :href="route('kuis.soal.index', kuis.id)" class="rounded-lg border px-4 py-2 text-sm dark:border-gray-700">Batal</Link>
                    <button :disabled="form.processing" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Soal' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>