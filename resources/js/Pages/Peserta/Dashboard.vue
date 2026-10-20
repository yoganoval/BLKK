<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import PesertaLayout from '@/Layouts/PesertaLayout.vue'

defineOptions({
    layout: PesertaLayout,
})

const props = defineProps({
    peserta: {
        type: Object,
        required: true,
    },
    pelatihanDiikuti: {
        type: Array,
        default: () => [],
    },
    nilai: {
        type: Array,
        default: () => [],
    },
})

const rataRataNilai = computed(() => {
    const data = props.nilai
        .map(item => Number(item.nilai))
        .filter(value => Number.isFinite(value))

    if (!data.length) return 0

    return Math.round(
        data.reduce((total, value) => total + value, 0) / data.length
    )
})
</script>

<template>
    <Head title="Dashboard Peserta" />

    <div class="space-y-6">
        <!-- HEADER HALAMAN -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Beranda / Dashboard
                </p>

                <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">
                    Dashboard Peserta
                </h1>

                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Selamat datang kembali, {{ peserta.nama }}.
                    Mari lanjutkan perjalanan belajarmu.
                </p>
            </div>

            <Link
                :href="route('peserta.materi.index')"
                class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
            >
                Mulai Belajar
            </Link>
        </div>

        <!-- STATISTIK -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Pelatihan Diikuti
                </p>

                <p class="mt-3 text-3xl font-bold text-indigo-600">
                    {{ pelatihanDiikuti.length }}
                </p>

                <p class="mt-2 text-xs text-slate-400">
                    Total program dalam daftar kamu
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Komponen Penilaian
                </p>

                <p class="mt-3 text-3xl font-bold text-emerald-600">
                    {{ nilai.length }}
                </p>

                <p class="mt-2 text-xs text-slate-400">
                    Nilai yang telah dicatat instruktur
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Rata-rata Nilai
                </p>

                <p class="mt-3 text-3xl font-bold text-violet-600">
                    {{ rataRataNilai }}
                    <span class="text-sm font-medium text-slate-400">/100</span>
                </p>

                <p class="mt-2 text-xs text-slate-400">
                    Berdasarkan nilai yang tersedia
                </p>
            </div>
        </div>

        <!-- PELATIHAN SAYA -->
        <section class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900 sm:p-6">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    Pelatihan Saya
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Daftar pelatihan yang kamu ikuti.
                </p>
            </div>

            <div
                v-if="pelatihanDiikuti.length"
                class="grid grid-cols-1 gap-4 lg:grid-cols-2"
            >
                <article
                    v-for="item in pelatihanDiikuti"
                    :key="item.pendaftaran_id"
                    class="rounded-xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50"
                >
                    <h3 class="font-semibold text-slate-800 dark:text-white">
                        {{ item.nama_pelatihan }}
                    </h3>

                    <div class="mt-3 space-y-2 text-sm text-slate-500 dark:text-slate-400">
                        <p>Tahap: {{ item.tahap || '-' }}</p>
                        <p>Kelas: {{ item.kelas || '-' }}</p>
                        <p>Status: {{ item.status || '-' }}</p>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="rounded-xl border border-dashed border-slate-300 px-4 py-10 text-center dark:border-slate-700"
            >
                <p class="font-semibold text-slate-700 dark:text-slate-200">
                    Belum ada pelatihan
                </p>

                <p class="mt-2 text-sm text-slate-500">
                    Data pelatihan akan tampil di sini.
                </p>
            </div>
        </section>

        <!-- NILAI SAYA -->
        <section class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900 sm:p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                Nilai Saya
            </h2>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Hasil penilaian dari instruktur.
            </p>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[650px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase text-slate-500 dark:border-slate-800">
                            <th class="px-3 py-3">No.</th>
                            <th class="px-3 py-3">Komponen</th>
                            <th class="px-3 py-3">Pelatihan</th>
                            <th class="px-3 py-3">Nilai</th>
                            <th class="px-3 py-3">Catatan</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(item, index) in nilai"
                            :key="item.id"
                            class="border-b border-slate-100 last:border-0 dark:border-slate-800"
                        >
                            <td class="px-3 py-4 text-slate-500">
                                {{ index + 1 }}
                            </td>

                            <td class="px-3 py-4 font-medium text-slate-800 dark:text-white">
                                {{ item.komponen || '-' }}
                            </td>

                            <td class="px-3 py-4 text-slate-500">
                                {{ item.nama_pelatihan || '-' }}
                            </td>

                            <td class="px-3 py-4">
                                <span class="rounded-lg bg-emerald-50 px-3 py-1.5 font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                    {{ item.nilai }}
                                </span>
                            </td>

                            <td class="px-3 py-4 text-slate-500">
                                {{ item.catatan || '-' }}
                            </td>
                        </tr>

                        <tr v-if="!nilai.length">
                            <td
                                colspan="5"
                                class="px-3 py-10 text-center text-slate-500"
                            >
                                Belum ada nilai yang diberikan oleh instruktur.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
