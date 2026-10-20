<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Soal {
    id: number
    pertanyaan: string
    tipe: string
    bobot: number | string
}

interface Kuis {
    id: number
    judul: string
    deskripsi: string | null
    program_pelatihan?: {
        nama?: string
        pelatihan?: {
            nama?: string
        }
    } | null
    kelas?: {
        nama: string
    } | null
    waktu_menit: number | null
    nilai_lulus: number | string
    percobaan_maksimal: number
    status: string
    soal?: Soal[]
}

defineProps<{
    kuis: Kuis
}>()

const statusLabel: Record<string, string> = {
    draft: 'Draft',
    published: 'Dipublikasikan',
    closed: 'Ditutup',
}

const statusClass: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    published: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
    closed: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
}
</script>

<template>
    <Head title="Detail Kuis" />

    <div class="min-h-screen bg-gray-50 p-4 md:p-8 dark:bg-gray-950">
        <div class="mx-auto max-w-5xl space-y-6">

            <!-- Navigasi -->
            <Link
                :href="route('kuis.index')"
                class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400"
            >
                <span aria-hidden="true">←</span>
                Kembali ke daftar kuis
            </Link>

            <!-- Informasi utama kuis -->
            <section
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="flex flex-col justify-between gap-5 border-b border-gray-200 p-6 sm:flex-row sm:items-start dark:border-gray-800">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400">
                            Detail Kuis
                        </p>

                        <h1 class="mt-2 break-words text-2xl font-bold text-gray-900 dark:text-white">
                            {{ kuis.judul }}
                        </h1>

                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-500 dark:text-gray-400">
                            {{ kuis.deskripsi || 'Tidak ada deskripsi untuk kuis ini.' }}
                        </p>

                        <span
                            class="mt-4 inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                            :class="statusClass[kuis.status] ?? statusClass.draft"
                        >
                            {{ statusLabel[kuis.status] ?? kuis.status }}
                        </span>
                    </div>

                    <Link
                        :href="route('kuis.edit', { kui: kuis.id })"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-amber-600"
                    >
                        Edit Kuis
                    </Link>
                </div>

                <!-- Statistik dan informasi -->
                <div class="grid gap-px bg-gray-200 sm:grid-cols-2 lg:grid-cols-3 dark:bg-gray-800">
                    <div class="bg-white p-5 dark:bg-gray-900">
                        <p class="text-xs font-medium text-gray-500">Program Pelatihan</p>
                        <p class="mt-2 font-semibold text-gray-900 dark:text-white">
                            {{
                                kuis.program_pelatihan?.pelatihan?.nama
                                ?? kuis.program_pelatihan?.nama
                                ?? '-'
                            }}
                        </p>
                    </div>

                    <div class="bg-white p-5 dark:bg-gray-900">
                        <p class="text-xs font-medium text-gray-500">Kelas</p>
                        <p class="mt-2 font-semibold text-gray-900 dark:text-white">
                            {{ kuis.kelas?.nama ?? '-' }}
                        </p>
                    </div>

                    <div class="bg-white p-5 dark:bg-gray-900">
                        <p class="text-xs font-medium text-gray-500">Durasi Kuis</p>
                        <p class="mt-2 font-semibold text-gray-900 dark:text-white">
                            {{ kuis.waktu_menit ? `${kuis.waktu_menit} menit` : 'Tanpa batas waktu' }}
                        </p>
                    </div>

                    <div class="bg-white p-5 dark:bg-gray-900">
                        <p class="text-xs font-medium text-gray-500">Nilai Lulus</p>
                        <p class="mt-2 font-semibold text-gray-900 dark:text-white">
                            {{ kuis.nilai_lulus }}
                        </p>
                    </div>

                    <div class="bg-white p-5 dark:bg-gray-900">
                        <p class="text-xs font-medium text-gray-500">Maksimal Percobaan</p>
                        <p class="mt-2 font-semibold text-gray-900 dark:text-white">
                            {{ kuis.percobaan_maksimal }} kali
                        </p>
                    </div>

                    <div class="bg-white p-5 dark:bg-gray-900">
                        <p class="text-xs font-medium text-gray-500">Jumlah Soal</p>
                        <p class="mt-2 font-semibold text-gray-900 dark:text-white">
                            {{ kuis.soal?.length ?? 0 }} soal
                        </p>
                    </div>
                </div>
            </section>

            <!-- Daftar soal -->
            <section
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6 dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="mb-5 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            Daftar Soal
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Kelola pertanyaan, jenis soal, dan bobot nilai.
                        </p>
                    </div>

                    <Link
                        :href="route('kuis.soal.index', { kui: kuis.id })"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Kelola Soal
                    </Link>
                </div>

                <div
                    v-if="kuis.soal && kuis.soal.length > 0"
                    class="space-y-3"
                >
                    <article
                        v-for="(soal, index) in kuis.soal"
                        :key="soal.id"
                        class="rounded-xl border border-gray-200 p-4 dark:border-gray-800"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-gray-500">
                                Soal {{ index + 1 }}
                            </span>

                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ soal.tipe.replaceAll('_', ' ') }}
                                </span>

                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    Bobot: {{ soal.bobot }}
                                </span>
                            </div>
                        </div>

                        <p class="mt-3 whitespace-pre-line font-medium leading-6 text-gray-900 dark:text-white">
                            {{ soal.pertanyaan }}
                        </p>
                    </article>
                </div>

                <div
                    v-else
                    class="rounded-xl border border-dashed border-gray-300 px-5 py-10 text-center dark:border-gray-700"
                >
                    <p class="font-medium text-gray-700 dark:text-gray-300">
                        Belum ada soal pada kuis ini.
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        Tambahkan soal pilihan ganda, benar/salah, atau esai melalui menu Kelola Soal.
                    </p>
                </div>
            </section>
        </div>
    </div>
</template>
