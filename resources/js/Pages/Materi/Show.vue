<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Pelatihan {
    id: number;
    nama: string;
}

interface Kelas {
    id: number;
    nama: string;
}

interface Tahap {
    id: number;
    nama: string;
}

interface Creator {
    id: number;
    name: string;
}

interface MateriFile {
    id: number;
    nama_file: string;
    file_path: string;
    tipe_file: string | null;
    ukuran: number | null;
}

interface Materi {
    id: number;
    judul: string;
    deskripsi: string | null;
    isi_materi: string | null;
    urutan: number;
    status: 'draft' | 'published';
    created_at: string;
    updated_at: string;
    pelatihan?: Pelatihan;
    kelas?: Kelas;
    tahap?: Tahap | null;
    creator?: Creator;
    files?: MateriFile[];
}

interface Props {
    materi: Materi;
}

const props = defineProps<Props>();

function formatSize(size: number | null) {
    if (!size) return '-';

    if (size < 1024) {
        return `${size} KB`;
    }

    return `${(size / 1024).toFixed(1)} MB`;
}
</script>

<template>
    <Head :title="props.materi.judul" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('materi.index')"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-800"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </Link>

                    <div>
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-indigo-600 dark:text-indigo-400"
                        >
                            Materi Pembelajaran
                        </p>

                        <h1
                            class="text-xl font-bold text-slate-800 dark:text-white"
                        >
                            {{ props.materi.judul }}
                        </h1>
                    </div>
                </div>

                <Link
                    :href="route('materi.edit', props.materi.id)"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                        />
                    </svg>

                    Edit Materi
                </Link>
            </div>

            <!-- INFO -->
            <div
                class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4"
            >
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Pelatihan
                    </p>

                    <p
                        class="mt-1 font-semibold text-slate-800 dark:text-white"
                    >
                        {{ props.materi.pelatihan?.nama ?? '-' }}
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Kelas
                    </p>

                    <p
                        class="mt-1 font-semibold text-slate-800 dark:text-white"
                    >
                        {{ props.materi.kelas?.nama ?? '-' }}
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Tahap
                    </p>

                    <p
                        class="mt-1 font-semibold text-slate-800 dark:text-white"
                    >
                        {{ props.materi.tahap?.nama ?? '-' }}
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Status
                    </p>

                    <span
                        v-if="props.materi.status === 'published'"
                        class="mt-2 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"
                    >
                        Published
                    </span>

                    <span
                        v-else
                        class="mt-2 inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-600 dark:bg-amber-500/10 dark:text-amber-400"
                    >
                        Draft
                    </span>
                </div>
            </div>

            <!-- CONTENT -->
            <div class="space-y-6">

                <!-- DESCRIPTION -->
                <div
                    v-if="props.materi.deskripsi"
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2
                        class="mb-3 font-semibold text-slate-800 dark:text-white"
                    >
                        Deskripsi
                    </h2>

                    <p
                        class="whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300"
                    >
                        {{ props.materi.deskripsi }}
                    </p>
                </div>

                <!-- ISI -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h2
                                class="font-semibold text-slate-800 dark:text-white"
                            >
                                Isi Materi
                            </h2>

                            <p
                                class="text-sm text-slate-500 dark:text-slate-400"
                            >
                                Materi pembelajaran
                            </p>
                        </div>

                        <span
                            class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                        >
                            Materi {{ props.materi.urutan }}
                        </span>
                    </div>

                    <div
                        v-if="props.materi.isi_materi"
                        class="whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300"
                    >
                        {{ props.materi.isi_materi }}
                    </div>

                    <div
                        v-else
                        class="rounded-xl bg-slate-50 p-6 text-center text-sm text-slate-500 dark:bg-slate-800/50 dark:text-slate-400"
                    >
                        Belum ada isi materi.
                    </div>
                </div>

                <!-- FILE -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="mb-5">
                        <h2
                            class="font-semibold text-slate-800 dark:text-white"
                        >
                            File Materi
                        </h2>

                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            File pendukung pembelajaran
                        </p>
                    </div>

                    <div
                        v-if="props.materi.files?.length"
                        class="space-y-3"
                    >
                        <div
                            v-for="file in props.materi.files"
                            :key="file.id"
                            class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:bg-indigo-50/30 dark:border-slate-700 dark:hover:border-indigo-500/30 dark:hover:bg-indigo-500/5"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L18 9.828a4 4 0 10-5.657-5.657L5.05 11.464a6 6 0 108.485 8.485L19 14.485"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p
                                        class="truncate text-sm font-semibold text-slate-700 dark:text-slate-200"
                                    >
                                        {{ file.nama_file }}
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        {{ file.tipe_file ?? 'File' }}
                                        ·
                                        {{ formatSize(file.ukuran) }}
                                    </p>
                                </div>
                            </div>

                            <a
                                :href="`/storage/${file.file_path}`"
                                target="_blank"
                                class="shrink-0 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700"
                            >
                                Buka File
                            </a>
                        </div>
                    </div>

                    <div
                        v-else
                        class="rounded-xl bg-slate-50 p-6 text-center text-sm text-slate-500 dark:bg-slate-800/50 dark:text-slate-400"
                    >
                        Belum ada file pendukung.
                    </div>
                </div>

                <!-- META -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div
                        class="flex flex-col gap-2 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between dark:text-slate-400"
                    >
                        <span>
                            Dibuat oleh:
                            <strong class="text-slate-700 dark:text-slate-200">
                                {{ props.materi.creator?.name ?? '-' }}
                            </strong>
                        </span>

                        <span>
                            ID Materi: #{{ props.materi.id }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>