<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Kuis {
    id: number
    judul: string
    nilai_lulus: number
}

interface PercobaanKuis {
    id: number
    percobaan_ke: number
    nilai: number | null
    status: string
    lulus: boolean | null
    jumlah_benar: number
    jumlah_dijawab: number
    mulai_pada: string | null
    selesai_pada: string | null
    kuis: Kuis
}

defineProps<{
    percobaanKuis: PercobaanKuis
}>()
</script>

<template>
    <Head title="Hasil Kuis" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div
                class="px-6 py-10 text-center sm:px-10"
                :class="percobaanKuis.nilai === null
                    ? 'bg-amber-50 dark:bg-amber-950/30'
                    : percobaanKuis.lulus
                        ? 'bg-emerald-50 dark:bg-emerald-950/30'
                        : 'bg-red-50 dark:bg-red-950/30'"
            >
                <div
                    class="mx-auto flex size-16 items-center justify-center rounded-full text-3xl"
                    :class="percobaanKuis.nilai === null
                        ? 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200'
                        : percobaanKuis.lulus
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200'
                            : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200'"
                >
                    {{ percobaanKuis.nilai === null ? '⌛' : percobaanKuis.lulus ? '✓' : '×' }}
                </div>

                <h1 class="mt-5 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ percobaanKuis.nilai === null
                        ? 'Menunggu Koreksi'
                        : percobaanKuis.lulus
                            ? 'Selamat, Kamu Lulus!'
                            : 'Kuis Selesai' }}
                </h1>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ percobaanKuis.kuis.judul }}
                </p>

                <p
                    v-if="percobaanKuis.nilai !== null"
                    class="mt-6 text-5xl font-extrabold text-gray-900 dark:text-white"
                >
                    {{ Number(percobaanKuis.nilai).toFixed(2) }}
                </p>

                <p
                    v-else
                    class="mx-auto mt-5 max-w-lg text-sm leading-6 text-amber-800 dark:text-amber-200"
                >
                    Jawaban esai kamu masih menunggu koreksi instruktur. Nilai akhir dan status kelulusan akan ditetapkan setelah koreksi selesai.
                </p>
            </div>

            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Percobaan ke
                    </p>
                    <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                        {{ percobaanKuis.percobaan_ke }}
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Nilai kelulusan
                    </p>
                    <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                        {{ percobaanKuis.kuis.nilai_lulus }}
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Jawaban benar
                    </p>
                    <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                        {{ percobaanKuis.jumlah_benar }}
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Soal dijawab
                    </p>
                    <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                        {{ percobaanKuis.jumlah_dijawab }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-gray-200 p-6 dark:border-gray-700 sm:flex-row">
                <Link
                    :href="route('peserta.kuis.index')"
                    class="inline-flex flex-1 justify-center rounded-xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                >
                    Kembali ke Daftar Kuis
                </Link>

                <Link
                    :href="route('peserta.dashboard')"
                    class="inline-flex flex-1 justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    Dashboard Peserta
                </Link>
            </div>
        </div>
    </div>
</template>
