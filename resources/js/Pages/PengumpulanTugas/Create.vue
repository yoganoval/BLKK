<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'

interface Materi {
    id: number
    judul: string
}

const props = defineProps<{
    materis: Materi[]
}>()

const form = useForm({
    materi_id: '',
    judul: '',
    deskripsi: '',
    instruksi: '',
    tanggal_mulai: '',
    tanggal_deadline: '',
    nilai_maksimal: 100,
    status: 'draft',
})

const submit = () => {
    form.post(route('tugas.store'))
}
</script>

<template>
    <Head title="Tambah Tugas" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6">
                <Link
                    :href="route('tugas.index')"
                    class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700"
                >
                    ← Kembali
                </Link>

                <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                    Tambah Tugas
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Tambahkan tugas untuk peserta pelatihan
                </p>
            </div>

            <form @submit.prevent="submit">
                <div
                    class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <!-- MATERI -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                            Materi
                        </label>

                        <select
                            v-model="form.materi_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >
                            <option value="">Pilih Materi</option>

                            <option
                                v-for="materi in props.materis"
                                :key="materi.id"
                                :value="materi.id"
                            >
                                {{ materi.judul }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.materi_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.materi_id }}
                        </p>
                    </div>

                    <!-- JUDUL -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                            Judul Tugas
                        </label>

                        <input
                            v-model="form.judul"
                            type="text"
                            placeholder="Contoh: Praktik Membuat Dokumen Word"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        />

                        <p
                            v-if="form.errors.judul"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.judul }}
                        </p>
                    </div>

                    <!-- DESKRIPSI -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                            Deskripsi
                        </label>

                        <textarea
                            v-model="form.deskripsi"
                            rows="4"
                            placeholder="Deskripsi tugas..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        />
                    </div>

                    <!-- INSTRUKSI -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                            Instruksi
                        </label>

                        <textarea
                            v-model="form.instruksi"
                            rows="5"
                            placeholder="Tuliskan petunjuk pengerjaan tugas..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        />
                    </div>

                    <!-- TANGGAL -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Tanggal Mulai
                            </label>

                            <input
                                v-model="form.tanggal_mulai"
                                type="datetime-local"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Deadline
                            </label>

                            <input
                                v-model="form.tanggal_deadline"
                                type="datetime-local"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            />
                        </div>
                    </div>

                    <!-- NILAI + STATUS -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Nilai Maksimal
                            </label>

                            <input
                                v-model="form.nilai_maksimal"
                                type="number"
                                min="0"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Status
                            </label>

                            <select
                                v-model="form.status"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>

                    <!-- BUTTON -->
                    <div class="flex justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                        <Link
                            :href="route('tugas.index')"
                            class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:text-slate-300"
                        >
                            Batal
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Tugas' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>