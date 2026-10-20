<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

interface Kuis {
    id: number
    judul: string
}

const props = defineProps<{
    kuis: Kuis
}>()

const form = useForm({
    pertanyaan: '',
    tipe: 'pilihan_ganda',
    bobot: 1,
    urutan: 1,
})

function submit() {
    form.post(route('kuis.soal.store', { kui: props.kuis.id }), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Tambah Soal Kuis" />

    <div class="min-h-screen bg-gray-50 p-4 dark:bg-gray-950 md:p-8">
        <div class="mx-auto max-w-3xl space-y-6">
            <div>
                <Link
                    :href="route('kuis.soal.index', { kui: kuis.id })"
                    class="text-sm text-blue-600 hover:underline"
                >
                    ← Kembali ke daftar soal
                </Link>

                <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
                    Tambah Soal Kuis
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Kuis: {{ kuis.judul }}
                </p>
            </div>

            <form
                @submit.prevent="submit"
                class="space-y-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">
                        Pertanyaan
                    </label>

                    <textarea
                        v-model="form.pertanyaan"
                        rows="5"
                        required
                        placeholder="Tuliskan pertanyaan kuis..."
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    />

                    <p v-if="form.errors.pertanyaan" class="mt-1 text-sm text-red-500">
                        {{ form.errors.pertanyaan }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">
                        Tipe Soal
                    </label>

                    <select
                        v-model="form.tipe"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="pilihan_ganda">Pilihan Ganda</option>
                        <option value="benar_salah">Benar / Salah</option>
                        <option value="esai">Esai</option>
                    </select>

                    <p v-if="form.errors.tipe" class="mt-1 text-sm text-red-500">
                        {{ form.errors.tipe }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Bobot Nilai
                        </label>

                        <input
                            v-model.number="form.bobot"
                            type="number"
                            min="0.01"
                            max="999.99"
                            step="0.01"
                            required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />

                        <p v-if="form.errors.bobot" class="mt-1 text-sm text-red-500">
                            {{ form.errors.bobot }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Urutan Soal
                        </label>

                        <input
                            v-model.number="form.urutan"
                            type="number"
                            min="1"
                            step="1"
                            required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />

                        <p v-if="form.errors.urutan" class="mt-1 text-sm text-red-500">
                            {{ form.errors.urutan }}
                        </p>
                    </div>
                </div>

                <div class="rounded-xl bg-blue-50 p-4 text-sm text-blue-800 dark:bg-blue-950 dark:text-blue-200">
                    <p class="font-semibold">Informasi</p>
                    <p class="mt-1">
                        Formulir ini menyimpan pertanyaan, tipe soal, bobot,
                        dan urutan. Opsi jawaban pilihan ganda ditambahkan
                        setelah soal berhasil disimpan.
                    </p>
                </div>

                <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                    <Link
                        :href="route('kuis.soal.index', { kui: kuis.id })"
                        class="rounded-xl border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Soal' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
