<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
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
    pelatihan_id: number;
    kelas_id: number;
    tahap_id: number | null;
    judul: string;
    deskripsi: string | null;
    isi_materi: string | null;
    urutan: number;
    status: 'draft' | 'published';
    pelatihan?: Pelatihan;
    kelas?: Kelas;
    tahap?: Tahap | null;
    creator?: Creator;
    files?: MateriFile[];
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedMateri {
    data: Materi[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface Props {
    materis: PaginatedMateri;
    filters: {
        search?: string;
        pelatihan_id?: string | number;
        kelas_id?: string | number;
        tahap_id?: string | number;
        status?: string;
    };
    pelatihans: Pelatihan[];
    kelas: Kelas[];
    tahaps: Tahap[];
}

const props = defineProps<Props>();

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function formatFileSize(size: number | null) {
    if (!size) return '-';

    if (size < 1024) {
        return `${size} KB`;
    }

    return `${(size / 1024).toFixed(1)} MB`;
}

function filterData() {
    router.get(
        route('materi.index'),
        {
            search: props.filters.search || undefined,
            pelatihan_id: props.filters.pelatihan_id || undefined,
            kelas_id: props.filters.kelas_id || undefined,
            tahap_id: props.filters.tahap_id || undefined,
            status: props.filters.status || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

function resetFilter() {
    router.get(
        route('materi.index'),
        {},
        {
            preserveState: false,
        },
    );
}

function deleteMateri(id: number) {
    if (!confirm('Apakah Anda yakin ingin menghapus materi ini?')) {
        return;
    }

    router.delete(route('materi.destroy', id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Materi" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <div class="mb-2 flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M4 19.5A2.5 2.5 0 016.5 17H20"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"
                                />
                            </svg>
                        </div>

                        <div>
                            <h1
                                class="text-xl font-bold text-slate-800 dark:text-white"
                            >
                                Materi
                            </h1>

                            <p
                                class="text-sm text-slate-500 dark:text-slate-400"
                            >
                                Kelola materi pembelajaran peserta pelatihan
                            </p>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('materi.create')"
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
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    Tambah Materi
                </Link>
            </div>

            <!-- FILTER -->
            <div
                class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="mb-4 flex items-center gap-2">
                    <svg
                        class="h-5 w-5 text-slate-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 4h18M6 8h12M10 12h4M11 16h2M12 20v-4"
                        />
                    </svg>

                    <h2
                        class="text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Filter Materi
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">

                    <!-- SEARCH -->
                    <div class="lg:col-span-2">
                        <label
                            class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                        >
                            Cari Materi
                        </label>

                        <div class="relative">
                            <input
                                v-model="props.filters.search"
                                type="text"
                                placeholder="Cari judul materi..."
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                @keyup.enter="filterData"
                            />
                        </div>
                    </div>

                    <!-- PELATIHAN -->
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                        >
                            Pelatihan
                        </label>

                        <select
                            v-model="props.filters.pelatihan_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            @change="filterData"
                        >
                            <option value="">Semua Pelatihan</option>

                            <option
                                v-for="pelatihan in props.pelatihans"
                                :key="pelatihan.id"
                                :value="pelatihan.id"
                            >
                                {{ pelatihan.nama }}
                            </option>
                        </select>
                    </div>

                    <!-- KELAS -->
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                        >
                            Kelas
                        </label>

                        <select
                            v-model="props.filters.kelas_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            @change="filterData"
                        >
                            <option value="">Semua Kelas</option>

                            <option
                                v-for="item in props.kelas"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                            </option>
                        </select>
                    </div>

                    <!-- STATUS -->
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                        >
                            Status
                        </label>

                        <select
                            v-model="props.filters.status"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            @change="filterData"
                        >
                            <option value="">Semua Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                        @click="resetFilter"
                    >
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800"
                >
                    <div>
                        <h2
                            class="font-semibold text-slate-800 dark:text-white"
                        >
                            Daftar Materi
                        </h2>

                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Total {{ props.materis.total }} materi
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[950px] text-left text-sm">
                        <thead
                            class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800/60 dark:text-slate-400"
                        >
                            <tr>
                                <th class="px-5 py-4">No</th>
                                <th class="px-5 py-4">Materi</th>
                                <th class="px-5 py-4">Pelatihan</th>
                                <th class="px-5 py-4">Kelas</th>
                                <th class="px-5 py-4">Tahap</th>
                                <th class="px-5 py-4">File</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-slate-100 dark:divide-slate-800"
                        >
                            <tr
                                v-for="(materi, index) in props.materis.data"
                                :key="materi.id"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >
                                <td
                                    class="px-5 py-4 text-slate-500 dark:text-slate-400"
                                >
                                    {{
                                        (props.materis.current_page - 1) *
                                            props.materis.per_page +
                                        index +
                                        1
                                    }}
                                </td>

                                <td class="px-5 py-4">
                                    <div>
                                        <Link
                                            :href="
                                                route(
                                                    'materi.show',
                                                    materi.id,
                                                )
                                            "
                                            class="font-semibold text-slate-800 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400"
                                        >
                                            {{ materi.judul }}
                                        </Link>

                                        <p
                                            v-if="materi.deskripsi"
                                            class="mt-1 line-clamp-2 max-w-xs text-xs text-slate-500 dark:text-slate-400"
                                        >
                                            {{ materi.deskripsi }}
                                        </p>
                                    </div>
                                </td>

                                <td
                                    class="px-5 py-4 text-slate-600 dark:text-slate-300"
                                >
                                    {{ materi.pelatihan?.nama ?? '-' }}
                                </td>

                                <td
                                    class="px-5 py-4 text-slate-600 dark:text-slate-300"
                                >
                                    {{ materi.kelas?.nama ?? '-' }}
                                </td>

                                <td
                                    class="px-5 py-4 text-slate-600 dark:text-slate-300"
                                >
                                    {{ materi.tahap?.nama ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                                    >
                                        <svg
                                            class="h-4 w-4"
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

                                        {{ materi.files?.length ?? 0 }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        v-if="materi.status === 'published'"
                                        class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"
                                    >
                                        Published
                                    </span>

                                    <span
                                        v-else
                                        class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-600 dark:bg-amber-500/10 dark:text-amber-400"
                                    >
                                        Draft
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'materi.show',
                                                    materi.id,
                                                )
                                            "
                                            class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:hover:bg-slate-800 dark:hover:text-indigo-400"
                                            title="Detail"
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
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                />
                                            </svg>
                                        </Link>

                                        <Link
                                            :href="
                                                route(
                                                    'materi.edit',
                                                    materi.id,
                                                )
                                            "
                                            class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-amber-600 dark:hover:bg-slate-800 dark:hover:text-amber-400"
                                            title="Edit"
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
                                        </Link>

                                        <button
                                            type="button"
                                            class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                                            title="Hapus"
                                            @click="deleteMateri(materi.id)"
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
                                                    d="M6 7h12M10 11v6M14 11v6M9 7V4h6v3M5 7l1 13h12l1-13"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="props.materis.data.length === 0">
                                <td
                                    colspan="8"
                                    class="px-5 py-12 text-center"
                                >
                                    <div
                                        class="flex flex-col items-center justify-center"
                                    >
                                        <div
                                            class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800"
                                        >
                                            <svg
                                                class="h-7 w-7"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M4 19.5A2.5 2.5 0 016.5 17H20"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"
                                                />
                                            </svg>
                                        </div>

                                        <p
                                            class="font-medium text-slate-700 dark:text-slate-200"
                                        >
                                            Belum ada materi
                                        </p>

                                        <p
                                            class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                                        >
                                            Silakan tambahkan materi baru.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div
                    v-if="props.materis.last_page > 1"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-800"
                >
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Halaman {{ props.materis.current_page }} dari
                        {{ props.materis.last_page }}
                    </p>

                    <div class="flex flex-wrap gap-1">
                        <template
                            v-for="(link, index) in props.materis.links"
                            :key="index"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-lg px-3 py-1.5 text-sm transition"
                                :class="
                                    link.active
                                        ? 'bg-indigo-600 text-white'
                                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                                "
                                v-html="link.label"
                            />

                            <span
                                v-else
                                class="rounded-lg px-3 py-1.5 text-sm text-slate-300 dark:text-slate-600"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>