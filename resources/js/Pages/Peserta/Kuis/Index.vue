
<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PesertaLayout from '@/Layouts/PesertaLayout.vue'
import {
    ClipboardList,
    CheckCircle2,
    Clock3,
    BookOpen,
    ArrowRight,
    ArrowLeft,
    FileQuestion,
    Trophy,
    GraduationCap,
    Timer,
    Target,
    RotateCcw,
    Inbox,
} from 'lucide-vue-next'

defineOptions({
    layout: PesertaLayout,
})

interface ProgramPelatihan {
    nama?: string
    pelatihan?: {
        nama: string
    } | null
    tahap?: {
        nama: string
    } | null
}

interface Kelas {
    nama: string
}

interface Materi {
    judul: string
}

interface PercobaanKuis {
    id: number
    percobaan_ke: number
    nilai: number | string | null
    status: string
    lulus: boolean | number | null
    mulai_pada: string | null
    selesai_pada: string | null
}

interface Kuis {
    id: number
    judul: string
    deskripsi: string | null
    waktu_menit: number | null
    nilai_lulus: number | string
    percobaan_maksimal: number
    status: string
    soal_count: number
    jumlah_percobaan?: number
    percobaan_terakhir?: PercobaanKuis | null
    program_pelatihan?: ProgramPelatihan | null
    kelas?: Kelas | null
    materi?: Materi | null
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedKuis {
    data: Kuis[]
    links: PaginationLink[]
    current_page: number
    last_page: number
    total: number
}

const props = defineProps<{
    kuis: PaginatedKuis
}>()

const daftarKuis = computed(() => props.kuis.data ?? [])

const statistik = computed(() => ({
    total: props.kuis.total ?? 0,
    selesai: daftarKuis.value.filter(
        item => item.percobaan_terakhir?.status === 'selesai'
    ).length,
    berlangsung: daftarKuis.value.filter(
        item => item.percobaan_terakhir?.status === 'berlangsung'
    ).length,
    belumDikerjakan: daftarKuis.value.filter(
        item => !item.percobaan_terakhir
    ).length,
}))

function namaPelatihan(item: Kuis): string {
    return item.program_pelatihan?.pelatihan?.nama
        ?? item.program_pelatihan?.nama
        ?? 'Pelatihan'
}

function labelPagination(label: string): string {
    if (label.includes('Previous')) return '‹ Sebelumnya'
    if (label.includes('Next')) return 'Berikutnya ›'

    return label.replace(/&laquo;|&raquo;/g, '').trim()
}

function formatNilai(
    nilai: number | string | null | undefined
): string {
    if (nilai === null || nilai === undefined || nilai === '') {
        return '-'
    }

    const angka = Number(nilai)

    if (!Number.isFinite(angka)) return '-'

    return angka.toLocaleString('id-ID', {
        maximumFractionDigits: 2,
    })
}

function statusPercobaan(status: string): string {
    switch (status) {
        case 'selesai':
            return 'Selesai'
        case 'berlangsung':
            return 'Sedang dikerjakan'
        default:
            return status || 'Belum dikerjakan'
    }
}

function kelasStatus(status: string | undefined): string {
    switch (status) {
        case 'selesai':
            return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-400/20'
        case 'berlangsung':
            return 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-400/20'
        default:
            return 'bg-gray-100 text-gray-600 ring-gray-500/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600/30'
    }
}
</script>

<template>
    <Head title="Kuis Saya" />

    <div class="min-h-screen bg-gray-50 px-4 py-6 text-gray-900 dark:bg-gray-950 dark:text-gray-100 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">

            <!-- Header dashboard -->
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-blue-700 to-indigo-800 p-6 text-white shadow-lg sm:p-8">
                <div class="pointer-events-none absolute -right-12 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl" />
                <div class="pointer-events-none absolute -bottom-24 right-1/3 h-48 w-48 rounded-full bg-indigo-300/20 blur-3xl" />

                <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:items-center">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-blue-50">
                            <GraduationCap class="h-4 w-4" />
                            Pembelajaran Peserta
                        </div>

                        <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                            Kuis Saya
                        </h1>

                        <p class="mt-3 max-w-xl text-sm leading-6 text-blue-100 sm:text-base">
                            Uji pemahamanmu, pantau hasil percobaan, dan tingkatkan nilai pada setiap kuis pelatihan.
                        </p>

                        <Link
                            :href="route('peserta.dashboard')"
                            class="mt-6 inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20"
                        >
                            <ArrowLeft class="h-4 w-4" />
                            Kembali ke Dashboard
                        </Link>
                    </div>

                    <div class="hidden h-28 w-28 shrink-0 items-center justify-center rounded-3xl border border-white/20 bg-white/10 shadow-inner sm:flex">
                        <ClipboardList class="h-14 w-14 text-white" />
                    </div>
                </div>

                <div class="relative mt-8 grid grid-cols-2 gap-3 border-t border-white/20 pt-5 sm:grid-cols-3">
                    <div>
                        <p class="text-xs text-blue-100">Total kuis</p>
                        <p class="mt-1 text-2xl font-bold">{{ statistik.total }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-100">Halaman saat ini</p>
                        <p class="mt-1 text-2xl font-bold">
                            {{ kuis.current_page }}<span class="text-base font-medium text-blue-200"> / {{ kuis.last_page }}</span>
                        </p>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-xs text-blue-100">Tips belajar</p>
                        <p class="mt-1 text-sm font-semibold">
                            Kerjakan kuis sebelum batas waktu berakhir.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Statistik -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                            <BookOpen class="h-5 w-5" />
                        </div>
                        <span class="text-xs font-medium text-gray-400">Keseluruhan</span>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Total Kuis</p>
                    <p class="mt-1 text-3xl font-bold">{{ statistik.total }}</p>
                    <p class="mt-2 text-xs text-gray-400">Semua kuis pada daftar</p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300">
                            <CheckCircle2 class="h-5 w-5" />
                        </div>
                        <span class="text-xs font-medium text-gray-400">Selesai</span>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Kuis Selesai</p>
                    <p class="mt-1 text-3xl font-bold">{{ statistik.selesai }}</p>
                    <p class="mt-2 text-xs text-gray-400">Berdasarkan halaman aktif</p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300">
                            <Clock3 class="h-5 w-5" />
                        </div>
                        <span class="text-xs font-medium text-gray-400">Berlangsung</span>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Sedang Dikerjakan</p>
                    <p class="mt-1 text-3xl font-bold">{{ statistik.berlangsung }}</p>
                    <p class="mt-2 text-xs text-gray-400">Percobaan yang belum selesai</p>
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-300">
                            <FileQuestion class="h-5 w-5" />
                        </div>
                        <span class="text-xs font-medium text-gray-400">Belum mulai</span>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Belum Dikerjakan</p>
                    <p class="mt-1 text-3xl font-bold">{{ statistik.belumDikerjakan }}</p>
                    <p class="mt-2 text-xs text-gray-400">Belum memiliki percobaan</p>
                </article>
            </section>

            <!-- Daftar kuis -->
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h2 class="text-lg font-bold">Daftar Kuis</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Periksa informasi kuis dan hasil percobaan terakhirmu.
                        </p>
                    </div>

                    <div class="inline-flex w-fit items-center gap-2 rounded-lg bg-gray-100 px-3 py-2 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <ClipboardList class="h-4 w-4" />
                        {{ kuis.total }} kuis tersedia
                    </div>
                </div>

                <div v-if="daftarKuis.length" class="grid gap-5 p-5 sm:p-6 md:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="item in daftarKuis"
                        :key="item.id"
                        class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-gray-200 transition duration-200 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg dark:border-gray-800 dark:hover:border-blue-800"
                    >
                        <!-- Judul kartu -->
                        <div class="border-b border-blue-100 bg-gradient-to-br from-blue-50 to-indigo-50 p-5 dark:border-gray-800 dark:from-blue-950/40 dark:to-indigo-950/30">
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-semibold text-blue-700 shadow-sm dark:bg-gray-900 dark:text-blue-300">
                                    <FileQuestion class="h-3.5 w-3.5" />
                                    Kuis
                                </span>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ item.soal_count }} soal
                                </span>
                            </div>

                            <h3 class="mt-4 line-clamp-2 min-h-14 text-lg font-bold leading-7 text-gray-900 dark:text-white">
                                {{ item.judul }}
                            </h3>

                            <p class="mt-2 line-clamp-3 min-h-[3.75rem] text-sm leading-5 text-gray-600 dark:text-gray-300">
                                {{ item.deskripsi || 'Tidak ada deskripsi kuis.' }}
                            </p>
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            <!-- Detail pelatihan -->
                            <div class="space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-300">
                                        <GraduationCap class="h-4 w-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Program Pelatihan</p>
                                        <p class="mt-1 break-words text-sm font-semibold">{{ namaPelatihan(item) }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                            {{ item.program_pelatihan?.tahap?.nama ?? 'Tahap tidak ditentukan' }}
                                            <span v-if="item.kelas"> · {{ item.kelas.nama }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div v-if="item.materi" class="flex items-start gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-300">
                                        <BookOpen class="h-4 w-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Materi terkait</p>
                                        <p class="mt-1 break-words text-sm font-medium">{{ item.materi.judul }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Durasi dan nilai lulus -->
                            <div class="mt-5 grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-800/70">
                                    <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                        <Timer class="h-4 w-4" />
                                        <span class="text-xs">Durasi</span>
                                    </div>
                                    <p class="mt-2 text-sm font-bold text-gray-900 dark:text-white">
                                        {{ item.waktu_menit ? `${item.waktu_menit} menit` : 'Tanpa batas' }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-800/70">
                                    <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                        <Target class="h-4 w-4" />
                                        <span class="text-xs">Nilai lulus</span>
                                    </div>
                                    <p class="mt-2 text-sm font-bold text-gray-900 dark:text-white">
                                        {{ formatNilai(item.nilai_lulus) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Hasil percobaan -->
                            <div class="mt-5 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-sm font-bold">Hasil Percobaan</p>
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset"
                                        :class="kelasStatus(item.percobaan_terakhir?.status)"
                                    >
                                        {{ statusPercobaan(item.percobaan_terakhir?.status ?? '') }}
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Nilai terakhir</p>
                                        <p
                                            class="mt-1 text-2xl font-bold"
                                            :class="item.percobaan_terakhir?.nilai != null ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'"
                                        >
                                            {{ formatNilai(item.percobaan_terakhir?.nilai) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Percobaan</p>
                                        <p class="mt-1 text-xl font-bold">
                                            {{ item.jumlah_percobaan ?? 0 }}
                                            <span class="text-sm font-medium text-gray-400">/ {{ item.percobaan_maksimal }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-if="item.percobaan_terakhir?.status === 'selesai' && item.percobaan_terakhir.nilai != null"
                                    class="mt-4 flex items-start gap-2 border-t border-gray-100 pt-3 dark:border-gray-700"
                                >
                                    <Trophy
                                        class="mt-0.5 h-4 w-4 shrink-0"
                                        :class="item.percobaan_terakhir.lulus ? 'text-emerald-500' : 'text-amber-500'"
                                    />
                                    <p
                                        class="text-sm font-semibold"
                                        :class="item.percobaan_terakhir.lulus ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'"
                                    >
                                        {{ item.percobaan_terakhir.lulus ? 'Selamat, kamu lulus!' : 'Nilai belum mencapai batas kelulusan.' }}
                                    </p>
                                </div>

                                <div
                                    v-if="item.percobaan_terakhir?.status === 'berlangsung'"
                                    class="mt-4 flex items-start gap-2 border-t border-gray-100 pt-3 text-xs leading-5 text-amber-700 dark:border-gray-700 dark:text-amber-300"
                                >
                                    <Clock3 class="mt-0.5 h-4 w-4 shrink-0" />
                                    Kamu memiliki percobaan yang belum selesai. Buka detail untuk melanjutkannya.
                                </div>
                            </div>

                            <!-- Tombol -->
                            <div class="mt-auto pt-5">
                                <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                                    Maksimal {{ item.percobaan_maksimal }} kali percobaan
                                </p>

                                <Link
                                    :href="route('peserta.kuis.show', { kuis: item.id })"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900"
                                >
                                    <span>
                                        {{ item.percobaan_terakhir?.status === 'berlangsung'
                                            ? 'Lanjutkan Kuis'
                                            : 'Lihat Detail Kuis' }}
                                    </span>
                                    <ArrowRight class="h-4 w-4" />
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Kondisi kosong -->
                <div v-else class="px-5 py-16 text-center sm:px-6">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500">
                        <Inbox class="h-8 w-8" />
                    </div>
                    <h3 class="mt-5 text-lg font-bold">Belum Ada Kuis</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Belum ada kuis yang tersedia untuk program pelatihan dan kelas yang kamu ikuti.
                    </p>
                    <Link
                        :href="route('peserta.dashboard')"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Kembali ke Dashboard
                    </Link>
                </div>

                <!-- Pagination -->
                <div
                    v-if="kuis.links?.length > 3"
                    class="flex flex-col gap-4 border-t border-gray-200 px-5 py-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6"
                >
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Menampilkan halaman {{ kuis.current_page }} dari {{ kuis.last_page }}
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <template v-for="(link, index) in kuis.links" :key="index">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                class="rounded-lg border px-3 py-2 text-sm font-medium transition"
                                :class="link.active
                                    ? 'border-blue-600 bg-blue-600 text-white'
                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'"
                            >
                                {{ labelPagination(link.label) }}
                            </Link>
                            <span
                                v-else
                                class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-400 dark:border-gray-800 dark:text-gray-600"
                            >
                                {{ labelPagination(link.label) }}
                            </span>
                        </template>
                    </div>
                </div>
            </section>

            <div class="flex justify-center pb-2">
                <Link
                    :href="route('peserta.dashboard')"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali ke Dashboard Peserta
                </Link>
            </div>
        </div>
    </div>
</template>
