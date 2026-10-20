<script setup>
import { computed, watch } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    materis: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            current_page: 1,
            last_page: 1,
            total: 0,
            from: 0,
            to: 0,
        }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    programPelatihans: {
        type: Array,
        default: () => [],
    },
    kelass: {
        type: Array,
        default: () => [],
    },
    tahaps: {
        type: Array,
        default: () => [],
    },
})

const filterForm = useForm({
    search: props.filters.search ?? '',
    program_pelatihan_id: props.filters.program_pelatihan_id ?? '',
    kelas_id: props.filters.kelas_id ?? '',
    tahap_id: props.filters.tahap_id ?? '',
    status: props.filters.status ?? '',
})

const hasFilter = computed(() =>
    Boolean(
        filterForm.search ||
        filterForm.program_pelatihan_id ||
        filterForm.kelas_id ||
        filterForm.tahap_id ||
        filterForm.status
    )
)

function applyFilter() {
    filterForm.get(route('materi.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

function resetFilter() {
    filterForm.search = ''
    filterForm.program_pelatihan_id = ''
    filterForm.kelas_id = ''
    filterForm.tahap_id = ''
    filterForm.status = ''

    applyFilter()
}

function deleteMateri(materi) {
    if (!confirm(`Apakah kamu yakin ingin menghapus materi "${materi.judul}"?`)) {
        return
    }

    router.delete(route('materi.destroy', materi.id), {
        preserveScroll: true,
    })
}

function statusClass(status) {
    return status === 'published'
        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'
}

function statusLabel(status) {
    return status === 'published' ? 'Published' : 'Draft'
}

const daftarMateri = computed(() => props.materis?.data ?? [])
const paginationLinks = computed(() => props.materis?.links ?? [])

watch(
    () => props.filters,
    (filters) => {
        filterForm.search = filters?.search ?? ''
        filterForm.program_pelatihan_id = filters?.program_pelatihan_id ?? ''
        filterForm.kelas_id = filters?.kelas_id ?? ''
        filterForm.tahap_id = filters?.tahap_id ?? ''
        filterForm.status = filters?.status ?? ''
    }
)
</script>

<template>
    <Head title="Daftar Materi" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm">
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"
                            />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Daftar Materi
                        </h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Kelola materi berdasarkan program pelatihan dan kelas.
                        </p>
                    </div>
                </div>

                <Link
                    :href="route('materi.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
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

            <!-- FLASH MESSAGE -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- RINGKASAN -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Total Materi
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">
                        {{ materis.total ?? 0 }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Materi Published
                    </p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600">
                        {{ daftarMateri.filter(item => item.status === 'published').length }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Pada halaman ini
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Materi Draft
                    </p>
                    <p class="mt-2 text-2xl font-bold text-amber-600">
                        {{ daftarMateri.filter(item => item.status === 'draft').length }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Pada halaman ini
                    </p>
                </div>
            </div>

            <!-- FILTER -->
            <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4">
                    <h2 class="font-bold text-slate-900 dark:text-white">
                        Pencarian dan Filter
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Cari materi berdasarkan judul, program, kelas, tahap, atau status.
                    </p>
                </div>

                <form @submit.prevent="applyFilter" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Cari Judul
                            </label>
                            <input
                                v-model="filterForm.search"
                                type="search"
                                placeholder="Masukkan judul materi..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Program Pelatihan
                            </label>
                            <select
                                v-model="filterForm.program_pelatihan_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">Semua program</option>
                                <option
                                    v-for="program in programPelatihans"
                                    :key="program.id"
                                    :value="program.id"
                                >
                                    {{ program.nama }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Kelas
                            </label>
                            <select
                                v-model="filterForm.kelas_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">Semua kelas</option>
                                <option
                                    v-for="kelas in kelass"
                                    :key="kelas.id"
                                    :value="kelas.id"
                                >
                                    {{ kelas.nama }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Tahap
                            </label>
                            <select
                                v-model="filterForm.tahap_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">Semua tahap</option>
                                <option
                                    v-for="tahap in tahaps"
                                    :key="tahap.id"
                                    :value="tahap.id"
                                >
                                    {{ tahap.nama }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Status
                            </label>
                            <select
                                v-model="filterForm.status"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">Semua status</option>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button
                                type="submit"
                                :disabled="filterForm.processing"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-60"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <circle cx="11" cy="11" r="7" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4" />
                                </svg>
                                Filter
                            </button>

                            <button
                                v-if="hasFilter"
                                type="button"
                                @click="resetFilter"
                                class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <!-- TABEL MATERI -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                    <div>
                        <h2 class="font-bold text-slate-900 dark:text-white">
                            Data Materi
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Daftar materi yang tersimpan dalam sistem.
                        </p>
                    </div>

                    <span class="text-sm text-slate-500 dark:text-slate-400">
                        {{ materis.from ?? 0 }}–{{ materis.to ?? 0 }}
                        dari {{ materis.total ?? 0 }} data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-[1000px] w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800/70 dark:text-slate-400">
                            <tr>
                                <th class="px-5 py-4 font-semibold">No.</th>
                                <th class="px-5 py-4 font-semibold">Materi</th>
                                <th class="px-5 py-4 font-semibold">Program Pelatihan</th>
                                <th class="px-5 py-4 font-semibold">Kelas</th>
                                <th class="px-5 py-4 font-semibold">File</th>
                                <th class="px-5 py-4 font-semibold">Pembuat</th>
                                <th class="px-5 py-4 font-semibold">Status</th>
                                <th class="px-5 py-4 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="(materi, index) in daftarMateri"
                                :key="materi.id"
                                class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40"
                            >
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400">
                                    {{ (materis.from ?? 1) + index }}
                                </td>

                                <td class="max-w-xs px-5 py-4">
                                    <p class="font-semibold text-slate-900 dark:text-white">
                                        {{ materi.judul }}
                                    </p>
                                    <p class="mt-1 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">
                                        {{ materi.deskripsi || 'Tidak ada deskripsi' }}
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-medium text-slate-800 dark:text-slate-200">
                                        {{ materi.program_pelatihan?.nama ?? '-' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        {{ materi.program_pelatihan?.pelatihan?.nama ?? 'Pelatihan tidak tersedia' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        Tahap: {{ materi.program_pelatihan?.tahap?.nama ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        v-if="materi.kelas?.length"
                                        class="flex max-w-xs flex-wrap gap-1.5"
                                    >
                                        <span
                                            v-for="kelas in materi.kelas"
                                            :key="kelas.id"
                                            class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300"
                                        >
                                            {{ kelas.nama }}
                                        </span>
                                    </div>
                                    <span v-else class="text-slate-400">-</span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l7.071-7.071a4 4 0 00-5.657-5.657L5.757 10.757a6 6 0 108.485 8.485L21 12.485"
                                            />
                                        </svg>
                                        {{ materi.files?.length ?? 0 }} file
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ materi.creator?.name ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="statusClass(materi.status)"
                                    >
                                        {{ statusLabel(materi.status) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <Link
                                            :href="route('materi.show', materi.id)"
                                            title="Detail materi"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-indigo-950/50 dark:hover:text-indigo-300"
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
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"
                                                />
                                            </svg>
                                        </Link>

                                        <Link
                                            :href="route('materi.edit', materi.id)"
                                            title="Edit materi"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-amber-200 text-amber-600 transition hover:bg-amber-50 dark:border-amber-900 dark:text-amber-400 dark:hover:bg-amber-950/50"
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
                                                    stroke-width="2"
                                                    d="m16.862 4.487 2.651 2.651M4 20l4.5-1 11-11a2.121 2.121 0 00-3-3l-11 11L4 20z"
                                                />
                                            </svg>
                                        </Link>

                                        <button
                                            type="button"
                                            title="Hapus materi"
                                            @click="deleteMateri(materi)"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/50"
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
                                                    stroke-width="2"
                                                    d="M6 7h12m-10 0V5h8v2m-9 0 1 13h8l1-13M10 11v5m4-5v5"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="daftarMateri.length === 0">
                                <td colspan="8" class="px-5 py-16 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800">
                                            <svg
                                                class="h-7 w-7"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"
                                                />
                                            </svg>
                                        </div>

                                        <h3 class="font-semibold text-slate-900 dark:text-white">
                                            Belum ada materi
                                        </h3>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            {{
                                                hasFilter
                                                    ? 'Tidak ada materi yang sesuai dengan filter.'
                                                    : 'Silakan tambahkan materi pelatihan terlebih dahulu.'
                                            }}
                                        </p>

                                        <button
                                            v-if="hasFilter"
                                            type="button"
                                            @click="resetFilter"
                                            class="mt-4 text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                                        >
                                            Reset filter
                                        </button>

                                        <Link
                                            v-else
                                            :href="route('materi.create')"
                                            class="mt-4 text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                                        >
                                            + Tambah materi
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div
                    v-if="paginationLinks.length > 0 && materis.last_page > 1"
                    class="flex flex-col gap-4 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800"
                >
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Halaman {{ materis.current_page }} dari {{ materis.last_page }}
                    </p>

                    <div class="flex flex-wrap items-center gap-1.5">
                        <template
                            v-for="(link, index) in paginationLinks"
                            :key="index"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                class="inline-flex min-w-9 items-center justify-center rounded-lg border px-3 py-2 text-sm transition"
                                :class="link.active
                                    ? 'border-indigo-600 bg-indigo-600 font-semibold text-white'
                                    : 'border-slate-200 text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800'"
                            >
                                <span v-html="link.label"></span>
                            </Link>

                            <span
                                v-else
                                class="inline-flex min-w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-100 px-3 py-2 text-sm text-slate-300 dark:border-slate-800 dark:text-slate-600"
                            >
                                <span v-html="link.label"></span>
                            </span>
                        </template>
                    </div>
                </div>
            </section>

        </div>
    </div>
</template>
