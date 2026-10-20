
<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PesertaLayout from '@/Layouts/PesertaLayout.vue'

defineOptions({
    layout: PesertaLayout,
})

defineProps({
    materi: {
        type: Object,
        required: true,
    },
})

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}
</script>

<template>
    <Head :title="`${materi.judul} - Materi Pembelajaran`" />

    <div class="min-h-screen bg-gray-50 px-4 py-6 transition-colors duration-300 dark:bg-gray-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl space-y-6">

            <!-- Tombol kembali -->
            <div>
                <Link
                    :href="route('peserta.materi.index')"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-blue-800 dark:hover:bg-blue-950/40 dark:hover:text-blue-400"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                    Kembali ke daftar materi
                </Link>
            </div>

            <!-- Header materi -->
            <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 p-6 text-white shadow-lg sm:p-8">
                <div class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-20 right-1/3 h-48 w-48 rounded-full bg-indigo-300/20 blur-3xl"></div>

                <div class="relative">
                    <div class="mb-5 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/15 px-3 py-1.5 text-xs font-semibold backdrop-blur-sm">
                            <span class="h-2 w-2 rounded-full bg-green-300"></span>
                            Materi pembelajaran
                        </span>

                        <span
                            v-if="materi.urutan"
                            class="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-medium"
                        >
                            Materi ke-{{ materi.urutan }}
                        </span>
                    </div>

                    <h1 class="max-w-4xl text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl lg:text-4xl">
                        {{ materi.judul }}
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-blue-100 sm:text-base">
                        {{ materi.deskripsi || 'Pelajari materi berikut dengan saksama untuk menambah pemahamanmu.' }}
                    </p>

                    <div class="mt-7 flex flex-wrap gap-x-6 gap-y-3 border-t border-white/20 pt-5">
                        <div class="flex items-center gap-2 text-sm text-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Modul pembelajaran</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Belajar sesuai waktumu</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Informasi tambahan -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Program pelatihan
                        </p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                            {{
                                materi.program_pelatihan?.pelatihan?.nama
                                || materi.program_pelatihan?.nama
                                || materi.pelatihan?.nama
                                || 'Tidak ditentukan'
                            }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Tahap pelatihan
                        </p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                            {{
                                materi.program_pelatihan?.tahap?.nama
                                || materi.tahap?.nama
                                || 'Tidak ditentukan'
                            }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Isi materi -->
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-5 dark:border-gray-800 sm:px-7">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            Isi Materi
                        </h2>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                            Baca dan pahami materi pembelajaran berikut.
                        </p>
                    </div>
                </div>

                <div class="p-5 sm:p-8 lg:p-10">
                    <div
                        v-if="materi.isi_materi"
                        class="whitespace-pre-wrap break-words text-sm leading-8 text-gray-700 dark:text-gray-300 sm:text-base sm:leading-9"
                    >{{ materi.isi_materi }}</div>

                    <div
                        v-else
                        class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center dark:border-gray-700 dark:bg-gray-800/60"
                    >
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                            </svg>
                        </div>

                        <h3 class="mt-4 font-semibold text-gray-900 dark:text-white">
                            Isi materi belum tersedia
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Instruktur belum menambahkan isi materi ini.
                            Silakan periksa kembali nanti.
                        </p>
                    </div>
                </div>
            </section>

            <!-- File pendukung -->
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-5 dark:border-gray-800 sm:px-7">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l7.07-7.07a4 4 0 00-5.656-5.657l-7.07 7.071a6 6 0 108.485 8.485L20 11.5" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                                File Materi
                            </h2>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                Dokumen dan file pendukung pembelajaran.
                            </p>
                        </div>
                    </div>

                    <span class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        {{ materi.files?.length ?? 0 }} file
                    </span>
                </div>

                <div class="p-5 sm:p-7">
                    <div
                        v-if="materi.files?.length"
                        class="grid grid-cols-1 gap-3 md:grid-cols-2"
                    >
                        <div
                            v-for="file in materi.files"
                            :key="file.id"
                            class="flex min-w-0 items-center gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-blue-300 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-950/20"
                        >
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6M8 13h8M8 17h8" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ file.nama_file || file.name || file.path }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    File pendukung materi
                                </p>
                            </div>

                            <a
                                :href="`/storage/${file.path}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                :aria-label="`Buka file ${file.nama_file || file.name || file.path}`"
                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-600 hover:text-white dark:bg-blue-950/50 dark:text-blue-400 dark:hover:bg-blue-600 dark:hover:text-white"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 3h7v7m-1-6L10 14" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div
                        v-else
                        class="rounded-xl border border-dashed border-gray-300 px-6 py-8 text-center dark:border-gray-700"
                    >
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Belum ada file pendukung untuk materi ini.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Navigasi bawah -->
            <div class="flex flex-col justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 sm:flex-row sm:items-center sm:px-7">
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        Sudah selesai membaca?
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Pastikan kamu memahami isi materi sebelum melanjutkan.
                    </p>
                </div>

                <Link
                    :href="route('peserta.materi.index')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900"
                >
                    Kembali ke Materi
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>

        </div>
    </div>
</template>
