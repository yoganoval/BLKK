<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Pelatihan {
    nama: string
}

interface Tahap {
    nama: string
}

interface ProgramPelatihan {
    pelatihan?: Pelatihan | null
    tahap?: Tahap | null
}

interface Kelas {
    nama: string
}

interface Materi {
    judul: string
}

interface Kuis {
    id: number
    judul: string
    deskripsi?: string | null
    waktu_menit: number
    nilai_lulus: number
    percobaan_maksimal: number
    jumlah_soal?: number | null
    soal_count: number
    status: string
    programPelatihan?: ProgramPelatihan | null
    kelas?: Kelas | null
    materi?: Materi | null
}

interface PercobaanTerakhir {
    id: number
    percobaan_ke: number
    nilai: number | null
    status: string
    lulus: boolean | null
    mulai_pada?: string | null
    selesai_pada?: string | null
}

interface Flash {
    error?: string
    success?: string
}

const props = defineProps<{
    kuis: Kuis
    jumlahPercobaan?: number
    percobaanTerakhir?: PercobaanTerakhir | null
    auth?: {
        peserta?: unknown
    }
    flash?: Flash
}>()

// Jumlah soal dihitung dari seluruh soal yang terdaftar pada kuis.
const totalSoal = computed(() => Math.max(0, props.kuis.soal_count ?? 0))

const maksimalPercobaan = computed(() =>
    Math.max(1, props.kuis.percobaan_maksimal ?? 1),
)

const jumlahPercobaan = computed(() =>
    Math.max(0, props.jumlahPercobaan ?? 0),
)

const sisaPercobaan = computed(() =>
    Math.max(0, maksimalPercobaan.value - jumlahPercobaan.value),
)

const percobaanSedangBerlangsung = computed(
    () => props.percobaanTerakhir?.status === 'berlangsung',
)

const batasPercobaanHabis = computed(
    () => sisaPercobaan.value <= 0 && !percobaanSedangBerlangsung.value,
)

const bolehMulai = computed(
    () =>
        totalSoal.value > 0 &&
        props.kuis.status === 'published' &&
        !batasPercobaanHabis.value,
)

const namaPelatihan = computed(
    () =>
        props.kuis.programPelatihan?.pelatihan?.nama ??
        'Pelatihan',
)

const namaTahap = computed(
    () => props.kuis.programPelatihan?.tahap?.nama ?? 'Tidak ditentukan',
)

const namaKelas = computed(
    () => props.kuis.kelas?.nama ?? 'Tidak ditentukan',
)

const formatNilai = (nilai: number | null | undefined) => {
    if (nilai === null || nilai === undefined) {
        return 'Menunggu koreksi'
    }

    return Number(nilai).toLocaleString('id-ID', {
        maximumFractionDigits: 2,
    })
}
</script>

<template>
    <Head :title="`Detail Kuis - ${kuis.judul}`" />

    <div class="min-h-screen bg-gray-50 px-4 py-6 dark:bg-gray-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl space-y-6">
            <!-- Navigasi -->
            <div>
                <Link
                    :href="route('peserta.kuis.index')"
                    class="inline-flex items-center gap-2 rounded-lg text-sm font-medium text-gray-600 transition hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
                >
                    <span aria-hidden="true">←</span>
                    Kembali ke daftar kuis
                </Link>
            </div>

            <!-- Pesan status -->
            <div
                v-if="flash?.error"
                role="alert"
                class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300"
            >
                {{ flash.error }}
            </div>

            <div
                v-if="flash?.success"
                role="status"
                class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-900 dark:bg-green-950/30 dark:text-green-300"
            >
                {{ flash.success }}
            </div>

            <!-- Header kuis -->
            <section
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-600 px-6 py-8 text-white sm:px-9 sm:py-10">
                    <div class="absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/10 blur-2xl" />

                    <div class="relative">
                        <div class="mb-4 flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-full border border-white/25 bg-white/15 px-3 py-1 text-xs font-semibold tracking-wide text-white"
                            >
                                DETAIL KUIS
                            </span>

                            <span
                                v-if="percobaanSedangBerlangsung"
                                class="rounded-full bg-amber-300 px-3 py-1 text-xs font-bold text-amber-950"
                            >
                                Sedang berlangsung
                            </span>

                            <span
                                v-else-if="batasPercobaanHabis"
                                class="rounded-full bg-red-200 px-3 py-1 text-xs font-bold text-red-900"
                            >
                                Percobaan habis
                            </span>
                        </div>

                        <h1 class="max-w-3xl text-2xl font-bold leading-tight sm:text-3xl lg:text-4xl">
                            {{ kuis.judul }}
                        </h1>

                        <p class="mt-4 max-w-2xl text-sm leading-7 text-indigo-100 sm:text-base">
                            {{ kuis.deskripsi || 'Kerjakan seluruh soal dengan teliti sebelum waktu pengerjaan berakhir.' }}
                        </p>

                        <div class="mt-6 flex flex-wrap gap-x-5 gap-y-3 text-sm text-indigo-100">
                            <span class="inline-flex items-center gap-2">
                                <span aria-hidden="true">📚</span>
                                {{ namaPelatihan }}
                            </span>

                            <span class="inline-flex items-center gap-2">
                                <span aria-hidden="true">🎓</span>
                                {{ namaTahap }}
                            </span>

                            <span class="inline-flex items-center gap-2">
                                <span aria-hidden="true">👥</span>
                                Kelas {{ namaKelas }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-7 p-5 sm:p-8">
                    <!-- Statistik -->
                    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50/70 p-4 dark:border-indigo-900/60 dark:bg-indigo-950/30">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-medium text-indigo-700 dark:text-indigo-300 sm:text-sm">
                                    Total soal
                                </p>
                                <span class="text-lg" aria-hidden="true">📝</span>
                            </div>

                            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ totalSoal }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Seluruh soal ditampilkan
                            </p>
                        </div>

                        <div class="rounded-xl border border-sky-100 bg-sky-50/70 p-4 dark:border-sky-900/60 dark:bg-sky-950/30">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-medium text-sky-700 dark:text-sky-300 sm:text-sm">
                                    Durasi
                                </p>
                                <span class="text-lg" aria-hidden="true">⏱️</span>
                            </div>

                            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ kuis.waktu_menit }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Menit pengerjaan
                            </p>
                        </div>

                        <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-4 dark:border-emerald-900/60 dark:bg-emerald-950/30">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300 sm:text-sm">
                                    Nilai lulus
                                </p>
                                <span class="text-lg" aria-hidden="true">🎯</span>
                            </div>

                            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ kuis.nilai_lulus }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Nilai minimum
                            </p>
                        </div>

                        <div class="rounded-xl border border-violet-100 bg-violet-50/70 p-4 dark:border-violet-900/60 dark:bg-violet-950/30">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-medium text-violet-700 dark:text-violet-300 sm:text-sm">
                                    Sisa percobaan
                                </p>
                                <span class="text-lg" aria-hidden="true">🔄</span>
                            </div>

                            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ jumlahPercobaan === 0 && !percobaanTerakhir ? maksimalPercobaan : sisaPercobaan }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Dari {{ maksimalPercobaan }} kesempatan
                            </p>
                        </div>
                    </div>

                    <!-- Informasi materi -->
                    <div
                        v-if="kuis.materi"
                        class="flex items-start gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-lg dark:bg-gray-800">
                            📖
                        </div>

                        <div class="min-w-0">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Materi terkait
                            </p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                {{ kuis.materi.judul }}
                            </p>
                        </div>
                    </div>

                    <!-- Percobaan terakhir -->
                    <div
                        v-if="percobaanTerakhir"
                        class="rounded-xl border border-gray-200 p-5 dark:border-gray-700"
                    >
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Percobaan terakhir
                        </h2>

                        <div class="mt-4 grid gap-4 sm:grid-cols-3">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Percobaan ke-
                                </p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                    {{ percobaanTerakhir.percobaan_ke }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Nilai
                                </p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                    {{ formatNilai(percobaanTerakhir.nilai) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Status
                                </p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                                    <template v-if="percobaanTerakhir.status === 'berlangsung'">
                                        Sedang dikerjakan
                                    </template>
                                    <template v-else-if="percobaanTerakhir.status === 'selesai'">
                                        {{ percobaanTerakhir.nilai === null ? 'Menunggu koreksi' : percobaanTerakhir.lulus ? 'Lulus' : 'Belum lulus' }}
                                    </template>
                                    <template v-else>
                                        {{ percobaanTerakhir.status }}
                                    </template>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Petunjuk -->
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/70 dark:bg-amber-950/20">
                        <div class="flex items-start gap-3">
                            <span class="text-xl" aria-hidden="true">💡</span>

                            <div>
                                <h2 class="font-semibold text-amber-900 dark:text-amber-200">
                                    Petunjuk pengerjaan
                                </h2>

                                <ul class="mt-3 list-inside list-disc space-y-2 text-sm leading-6 text-amber-900/90 dark:text-amber-100/90">
                                    <li>Semua soal yang tersedia pada kuis akan ditampilkan.</li>
                                    <li>Urutan soal dan pilihan jawaban diacak pada setiap percobaan baru.</li>
                                    <li>Urutan soal tetap sama jika kamu melanjutkan percobaan yang sedang berlangsung.</li>
                                    <li>Waktu dimulai ketika kamu memulai kuis dan tetap berjalan meskipun halaman dimuat ulang.</li>
                                    <li>Pastikan jawaban tersimpan sebelum menyelesaikan kuis.</li>
                                    <li>Jawaban esai mungkin memerlukan koreksi instruktur sebelum nilai akhir tersedia.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Peringatan -->
                    <div
                        v-if="totalSoal === 0"
                        class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-800 dark:border-red-900 dark:bg-red-950/30 dark:text-red-200"
                        role="alert"
                    >
                        <p class="font-semibold">
                            Kuis belum dapat dikerjakan
                        </p>
                        <p class="mt-1">
                            Belum ada soal yang terdaftar. Silakan hubungi instruktur untuk menambahkan soal.
                        </p>
                    </div>

                    <div
                        v-else-if="batasPercobaanHabis"
                        class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-800 dark:border-red-900 dark:bg-red-950/30 dark:text-red-200"
                        role="alert"
                    >
                        <p class="font-semibold">
                            Kesempatan pengerjaan sudah habis
                        </p>
                        <p class="mt-1">
                            Kamu telah menggunakan seluruh kesempatan untuk kuis ini.
                        </p>
                    </div>

                    <!-- Tombol aksi -->
                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                        <Link
                            :href="route('peserta.kuis.index')"
                            class="inline-flex min-h-12 items-center justify-center rounded-xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            ← Kembali ke daftar
                        </Link>

                        <Link
                            v-if="bolehMulai"
                            :href="route('peserta.kuis.start', { kuis: kuis.id })"
                            method="post"
                            as="button"
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                        >
                            {{ percobaanSedangBerlangsung ? 'Lanjutkan Kuis' : 'Mulai Kuis' }}
                            <span aria-hidden="true">→</span>
                        </Link>

                        <Link
                            v-else-if="percobaanTerakhir?.status === 'selesai'"
                            :href="route('peserta.kuis.result', { percobaanKuis: percobaanTerakhir.id })"
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-gray-800 px-6 py-3 text-sm font-semibold text-white transition hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600"
                        >
                            Lihat Hasil Kuis
                            <span aria-hidden="true">→</span>
                        </Link>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
