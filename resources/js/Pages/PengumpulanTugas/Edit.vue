<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Tugas {
    id: number
    judul: string
}

interface Peserta {
    id: number
    nama: string
}

interface Pengumpulan {
    id: number
    nama_file?: string
    file_path?: string
    nilai?: number
    catatan?: string
    umpan_balik?: string
    status: string
    tugas?: Tugas
    peserta?: Peserta
}

const props = defineProps<{
    pengumpulanTugas: Pengumpulan
}>()

const form = useForm({
    nilai: props.pengumpulanTugas.nilai ?? '',
    umpan_balik: props.pengumpulanTugas.umpan_balik ?? '',
    status: props.pengumpulanTugas.status,
})

const submit = () => {
    form.put(
        route(
            'pengumpulan-tugas.update',
            props.pengumpulanTugas.id,
        ),
    )
}
</script>

<template>
    <Head title="Penilaian Tugas" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">

            <div class="mb-6">
                <Link
                    :href="route('pengumpulan-tugas.index')"
                    class="mb-4 inline-flex text-sm font-medium text-indigo-600"
                >
                    ← Kembali
                </Link>

                <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                    Penilaian Tugas
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Periksa tugas dan berikan nilai kepada peserta
                </p>
            </div>

            <!-- INFORMASI -->
            <div
                class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <p class="text-xs text-slate-500">
                            Peserta
                        </p>

                        <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                            {{ props.pengumpulanTugas.peserta?.nama }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">
                            Tugas
                        </p>

                        <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                            {{ props.pengumpulanTugas.tugas?.judul }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="props.pengumpulanTugas.file_path"
                    class="mt-5 border-t border-slate-200 pt-5 dark:border-slate-800"
                >
                    <a
                        :href="`/storage/${props.pengumpulanTugas.file_path}`"
                        target="_blank"
                        class="inline-flex rounded-xl bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400"
                    >
                        📎
                        {{ props.pengumpulanTugas.nama_file || 'Buka File' }}
                    </a>
                </div>
            </div>

            <!-- PENILAIAN -->
            <form
                @submit.prevent="submit"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="space-y-5">

                    <div>
                        <label class="mb-2 block text-sm font-semibold">
                            Nilai
                        </label>

                        <input
                            v-model="form.nilai"
                            type="number"
                            min="0"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            placeholder="Masukkan nilai"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold">
                            Umpan Balik
                        </label>

                        <textarea
                            v-model="form.umpan_balik"
                            rows="5"
                            placeholder="Berikan komentar atau feedback..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold">
                            Status
                        </label>

                        <select
                            v-model="form.status"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >
                            <option value="dikumpulkan">
                                Dikumpulkan
                            </option>

                            <option value="dinilai">
                                Dinilai
                            </option>

                            <option value="revisi">
                                Revisi
                            </option>
                        </select>
                    </div>
                </div>

                <div
                    class="mt-6 flex justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-800"
                >
                    <Link
                        :href="route('pengumpulan-tugas.index')"
                        class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold dark:border-slate-700"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Penilaian' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>