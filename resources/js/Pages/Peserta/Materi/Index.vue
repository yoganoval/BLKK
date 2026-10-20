<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PesertaLayout from '@/Layouts/PesertaLayout.vue'

defineOptions({
    layout: PesertaLayout,
})

defineProps({
    materi: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            current_page: 1,
            last_page: 1,
            total: 0,
        }),
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
    <Head title="Materi Pembelajaran" />

    <div class="space-y-6 p-4 sm:p-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Materi Pembelajaran
            </h1>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Pelajari materi yang telah diterbitkan oleh instruktur.
            </p>
        </div>

        <!-- Statistik -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm
                   dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-12 w-12 items-center justify-center
                           rounded-lg bg-blue-100 text-blue-600
                           dark:bg-blue-900/40 dark:text-blue-400"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477
                               9.246 5 7.5 5S4.168 5.477
                               3 6.253v13C4.168 18.477
                               5.754 18 7.5 18s3.332.477
                               4.5 1.253m0-13C13.168
                               5.477 14.754 5 16.5 5S19.832
                               5.477 21 6.253v13C19.832
                               18.477 18.246 18 16.5 18s-3.332
                               .477-4.5 1.253"
                        />
                    </svg>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total Materi Tersedia
                    </p>

                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ materi.total ?? materi.data?.length ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Daftar Materi -->
        <div
            v-if="materi.data?.length"
            class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3"
        >
            <article
                v-for="item in materi.data"
                :key="item.id"
                class="flex flex-col overflow-hidden rounded-xl border
                       border-gray-200 bg-white shadow-sm transition
                       hover:-translate-y-1 hover:shadow-md
                       dark:border-gray-700 dark:bg-gray-800"
            >
                <!-- Bagian atas kartu -->
                <div class="border-b border-gray-100 p-5 dark:border-gray-700">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <span
                            class="rounded-full bg-green-100 px-3 py-1
                                   text-xs font-semibold text-green-700
                                   dark:bg-green-900/30 dark:text-green-400"
                        >
                            Materi tersedia
                        </span>

                        <span
                            v-if="item.urutan"
                            class="text-xs text-gray-500 dark:text-gray-400"
                        >
                            Materi {{ item.urutan }}
                        </span>
                    </div>

                    <h2
                        class="text-lg font-bold text-gray-900 dark:text-white"
                    >
                        {{ item.judul }}
                    </h2>

                    <p
                        class="mt-2 line-clamp-3 whitespace-pre-line
                               text-sm leading-6 text-gray-600
                               dark:text-gray-300"
                    >
                        {{ item.deskripsi || 'Tidak ada deskripsi materi.' }}
                    </p>
                </div>

                <!-- Informasi materi -->
                <div class="flex-1 space-y-3 p-5">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Program Pelatihan
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{
                                item.program_pelatihan?.pelatihan?.nama
                                || item.program_pelatihan?.nama
                                || '-'
                            }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Tahap / Putaran
                            </p>

                            <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ item.program_pelatihan?.tahap?.nama || '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Jumlah File
                            </p>

                            <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ item.files?.length ?? 0 }} file
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Kelas
                        </p>

                        <div
                            v-if="item.kelas?.length"
                            class="mt-1 flex flex-wrap gap-1.5"
                        >
                            <span
                                v-for="kelas in item.kelas"
                                :key="kelas.id"
                                class="rounded-md bg-gray-100 px-2 py-1
                                       text-xs text-gray-700
                                       dark:bg-gray-700 dark:text-gray-200"
                            >
                                {{ kelas.nama }}
                            </span>
                        </div>

                        <p
                            v-else
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            -
                        </p>
                    </div>
                </div>

                <!-- Tombol detail -->
                <div class="p-5 pt-0">
                    <Link
                        :href="route('peserta.materi.show', item.id)"
                        class="inline-flex w-full items-center justify-center
                               gap-2 rounded-lg bg-blue-600 px-4 py-2.5
                               text-sm font-semibold text-white transition
                               hover:bg-blue-700"
                    >
                        Pelajari Materi

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
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </Link>
                </div>
            </article>
        </div>

        <!-- Kondisi kosong -->
        <div
            v-else
            class="rounded-xl border border-dashed border-gray-300
                   bg-white px-6 py-16 text-center
                   dark:border-gray-700 dark:bg-gray-800"
        >
            <div
                class="mx-auto flex h-16 w-16 items-center justify-center
                       rounded-full bg-gray-100 text-gray-400
                       dark:bg-gray-700 dark:text-gray-300"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6.253v13m0-13C10.832
                           5.477 9.246 5 7.5 5S4.168
                           5.477 3 6.253v13C4.168
                           18.477 5.754 18 7.5 18s3.332
                           .477 4.5 1.253m0-13C13.168
                           5.477 14.754 5 16.5 5S19.832
                           5.477 21 6.253v13C19.832
                           18.477 18.246 18 16.5
                           18s-3.332.477-4.5 1.253"
                    />
                </svg>
            </div>

            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
                Belum Ada Materi
            </h3>

            <p class="mx-auto mt-2 max-w-md text-sm text-gray-500 dark:text-gray-400">
                Materi pembelajaran belum tersedia. Silakan kembali lagi
                nanti setelah instruktur menerbitkan materi.
            </p>
        </div>

        <!-- Pagination -->
        <div
            v-if="materi.links?.length > 3"
            class="flex flex-wrap items-center justify-center gap-1"
        >
            <template
                v-for="(link, index) in materi.links"
                :key="index"
            >
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    class="rounded-lg border px-3 py-2 text-sm transition"
                    :class="[
                        link.active
                            ? 'border-blue-600 bg-blue-600 text-white'
                            : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'
                    ]"
                >
                    <span v-html="link.label"></span>
                </Link>

                <span
                    v-else
                    class="cursor-not-allowed rounded-lg border
                           border-gray-200 px-3 py-2 text-sm
                           text-gray-400 dark:border-gray-700"
                    v-html="link.label"
                ></span>
            </template>
        </div>
    </div>
</template>
