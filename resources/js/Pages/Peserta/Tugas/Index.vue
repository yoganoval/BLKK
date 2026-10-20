
<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PesertaLayout from '@/Layouts/PesertaLayout.vue'

defineOptions({
    layout: PesertaLayout,
})

const props = defineProps({
    tugas: {
        type: Object,
        required: true,
    },
})

const formatTanggal = (tanggal) => {
    if (!tanggal) return 'Belum dikumpulkan'

    const parsed = new Date(
        /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(tanggal)
            ? tanggal.replace(' ', 'T')
            : tanggal
    )

    if (Number.isNaN(parsed.getTime())) return tanggal

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
        hour12: false,
    }).format(parsed)
}

const pengumpulanPeserta = (item) => {
    const daftar = item.pengumpulan_tugas ?? []
    return daftar.length ? daftar[0] : null
}

const statusLabel = (status) => {
    const labels = {
        dikumpulkan: 'Dikumpulkan',
        terlambat: 'Terlambat',
        dinilai: 'Sudah dinilai',
        diperiksa: 'Sedang diperiksa',
    }

    return labels[status] ?? status ?? 'Belum dikumpulkan'
}

const statusClass = (status) => {
    const classes = {
        dinilai:
            'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950/50 dark:text-emerald-400 dark:ring-emerald-500/20',
        terlambat:
            'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-950/50 dark:text-red-400 dark:ring-red-500/20',
        dikumpulkan:
            'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-950/50 dark:text-blue-400 dark:ring-blue-500/20',
        diperiksa:
            'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950/50 dark:text-amber-400 dark:ring-amber-500/20',
    }

    return classes[status] ??
        'bg-gray-100 text-gray-600 ring-gray-500/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600/30'
}

const statistik = (status) => {
    return (props.tugas.data ?? []).filter((item) => {
        return pengumpulanPeserta(item)?.status === status
    }).length
}

const totalTugas = () => (props.tugas.data ?? []).length
const totalBelumDikumpulkan = () => {
    return (props.tugas.data ?? []).filter((item) => {
        return !pengumpulanPeserta(item)
    }).length
}
</script>

<template>
    <Head title="Daftar Tugas" />

    <div class="min-h-screen bg-gray-50 px-4 py-6 transition-colors duration-300 dark:bg-gray-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-7">

            <!-- Header -->
            <header class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 p-6 text-white shadow-lg sm:p-8">
                <div class="pointer-events-none absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-20 right-1/3 h-48 w-48 rounded-full bg-indigo-300/20 blur-3xl"></div>

                <div class="relative flex flex-col justify-between gap-6 md:flex-row md:items-center">
                    <div>
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                            </svg>
                            LMS BLKK
                        </div>

                        <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">
                            Tugas Pembelajaran
                        </h1>

                        <p class="mt-3 max-w-2xl text-sm leading-6 text-blue-100 sm:text-base">
                            Kelola tugas pelatihanmu, perhatikan batas waktu pengumpulan,
                            dan pantau hasil penilaian dari instruktur.
                        </p>
                    </div>

                    <Link
                        :href="route('peserta.dashboard')"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/10 px-4 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5M5.25 9v11.25h13.5V9M9 20.25v-7.5h6v7.5" />
                        </svg>
                        Dashboard
                    </Link>
                </div>
            </header>

            <!-- Statistik -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tugas di halaman ini</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ totalTugas() }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">Jumlah tugas yang ditampilkan</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum dikumpulkan</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ totalBelumDikumpulkan() }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">Perlu diperhatikan</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sudah dikumpulkan</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ statistik('dikumpulkan') + statistik('terlambat') + statistik('diperiksa') + statistik('dinilai') }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">Termasuk tugas yang diperiksa</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sudah dinilai</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ statistik('dinilai') }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15l-2 5-1.5-3.5L5 15l5-1 2-4 2 4 5 1-3.5 1.5L16 20l-4-5zM12 2v3m8 1l-2 2M4 6l2 2" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">Hasil penilaian instruktur</p>
                </div>
            </section>

            <!-- Judul daftar -->
            <section>
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            Daftar Tugas
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Pilih tugas untuk melihat instruksi dan mengumpulkan jawaban.
                        </p>
                    </div>

                    <span class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                        {{ tugas.total ?? tugas.data?.length ?? 0 }} tugas total
                    </span>
                </div>

                <!-- Kartu tugas -->
                <div
                    v-if="tugas.data?.length"
                    class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3"
                >
                    <article
                        v-for="item in tugas.data"
                        :key="item.id"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-blue-900"
                    >
                        <div class="h-1.5 bg-gradient-to-r from-blue-600 to-indigo-500"></div>

                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                            <!-- Judul dan status -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                                    </svg>
                                </div>

                                <span
                                    class="inline-flex max-w-[145px] items-center gap-1.5 rounded-full px-3 py-1.5 text-center text-xs font-semibold ring-1 ring-inset"
                                    :class="statusClass(pengumpulanPeserta(item)?.status)"
                                >
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current"></span>
                                    {{ statusLabel(pengumpulanPeserta(item)?.status) }}
                                </span>
                            </div>

                            <h3 class="mt-4 break-words text-lg font-bold leading-7 text-gray-900 transition group-hover:text-blue-700 dark:text-white dark:group-hover:text-blue-400">
                                {{ item.judul }}
                            </h3>

                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-gray-500 dark:text-gray-400">
                                {{
                                    item.deskripsi
                                    || item.instruksi
                                    || 'Buka detail tugas untuk membaca instruksi pengerjaan.'
                                }}
                            </p>

                            <div class="mt-4 rounded-xl bg-gray-50 p-3 dark:bg-gray-800/70">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">
                                    Program pelatihan
                                </p>
                                <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    {{
                                        item.program_pelatihan?.pelatihan?.nama
                                        ?? item.program_pelatihan?.pelatihan?.nama_pelatihan
                                        ?? 'Program pelatihan'
                                    }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Kelas: {{ item.kelas?.nama ?? '-' }}
                                </p>
                            </div>

                            <!-- Deadline -->
                            <div class="mt-4 flex items-start gap-3">
                                <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Batas pengumpulan</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                        {{ formatTanggal(item.deadline) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Informasi pengumpulan -->
                            <div class="mt-4 space-y-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Waktu dikumpulkan
                                    </span>
                                    <span class="max-w-[60%] text-right text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ formatTanggal(pengumpulanPeserta(item)?.dikumpulkan_at) }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Nilai</span>
                                    <span class="text-right text-sm font-bold text-gray-900 dark:text-white">
                                        <template v-if="pengumpulanPeserta(item)?.nilai != null">
                                            {{ pengumpulanPeserta(item).nilai }}
                                            <span class="font-normal text-gray-400">
                                                / {{ item.nilai_maksimal ?? '-' }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span class="font-medium text-gray-400">Belum dinilai</span>
                                        </template>
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol detail -->
                            <div class="mt-auto pt-5">
                                <Link
                                    :href="route('peserta.tugas.show', item.id)"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900"
                                >
                                    Lihat Detail Tugas
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Kondisi kosong -->
                <div
                    v-else
                    class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center dark:border-gray-700 dark:bg-gray-900"
                >
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                        </svg>
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-gray-900 dark:text-white">
                        Belum Ada Tugas
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Tugas yang diterbitkan instruktur untuk program dan kelasmu
                        akan muncul di halaman ini.
                    </p>

                    <Link
                        :href="route('peserta.dashboard')"
                        class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Kembali ke Dashboard
                    </Link>
                </div>
            </section>

            <!-- Pagination -->
            <nav
                v-if="tugas.links?.length > 3"
                class="flex flex-wrap items-center justify-center gap-2"
                aria-label="Navigasi halaman tugas"
            >
                <template
                    v-for="(link, index) in tugas.links"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        preserve-scroll
                        class="rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                        :class="link.active
                            ? 'border-blue-600 bg-blue-600 text-white shadow-sm'
                            : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-blue-800 dark:hover:bg-blue-950/40'"
                    >
                        <span v-html="link.label"></span>
                    </Link>

                    <span
                        v-else
                        class="cursor-not-allowed rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-600"
                        v-html="link.label"
                    ></span>
                </template>
            </nav>
        </div>
    </div>
</template>
