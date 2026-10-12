<script setup>
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pesertas: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
        }),
    },
    totalLembaga: {
        type: Number,
        default: 0,
    },
})

const search = ref(props.filters?.search ?? '')

watch(
    () => props.filters?.search,
    (value) => {
        search.value = value ?? ''
    }
)

// Pencarian melalui controller Laravel
const submitSearch = () => {
    router.get(
        route('pesertas.index'),
        {
            search: search.value.trim(),
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

// Reset pencarian
const resetSearch = () => {
    search.value = ''

    router.get(
        route('pesertas.index'),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

// Hapus peserta
const deletePeserta = (peserta) => {
    if (
        !confirm(
            `Apakah Anda yakin ingin menghapus peserta "${peserta.nama}"?`
        )
    ) {
        return
    }

    router.delete(route('pesertas.destroy', peserta.id))
}
</script>

<template>
    <Head title="Peserta" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <span>Master Data</span>
                        <span>/</span>
                        <span class="font-medium text-slate-700 dark:text-slate-200">
                            Peserta
                        </span>
                    </div>

                    <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                        Peserta
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Kelola data peserta pelatihan.
                    </p>
                </div>

                <Link
                    :href="route('pesertas.create')"
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

                    Tambah Peserta
                </Link>
            </div>

            <!-- SUMMARY -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">

                <!-- TOTAL PESERTA -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Total Peserta
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                                {{ pesertas.total ?? 0 }}
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Jumlah seluruh data peserta
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
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
                                    d="M17 20h5v-2a4 4 0 00-4-4h-1m-4 6H7v-2a4 4 0 014-4h2a4 4 0 014 4v2zm-3-10a4 4 0 11-8 0 4 4 0 018 0zm8 1a3 3 0 10-6 0"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- LEMBAGA -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Lembaga Terdaftar
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                                {{ totalLembaga }}
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Jumlah lembaga unik
                            </p>
                        </div>

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
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
                                    d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 14v.01M15 17v.01"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- TABLE CARD -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <!-- SEARCH -->
                <div class="border-b border-slate-200 p-4 dark:border-slate-800 sm:p-5">
                    <form
                        @submit.prevent="submitSearch"
                        class="flex flex-col gap-2 sm:flex-row"
                    >
                        <div class="relative w-full sm:max-w-md">
                            <svg
                                class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m21 21-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>

                            <input
                                v-model="search"
                                type="search"
                                placeholder="Cari nama, username, kamar, lembaga..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            />
                        </div>

                        <div class="flex gap-2">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                            >
                                Cari
                            </button>

                            <button
                                v-if="search || filters.search"
                                type="button"
                                @click="resetSearch"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                            >
                                Reset
                            </button>
                        </div>
                    </form>

                    <p
                        v-if="filters.search"
                        class="mt-3 text-sm text-slate-500 dark:text-slate-400"
                    >
                        Hasil pencarian untuk:
                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                            {{ filters.search }}
                        </span>
                    </p>
                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">

                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    #
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Peserta
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Username
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Kamar
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Lembaga
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Alamat
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                            <tr
                                v-for="(peserta, index) in (pesertas.data ?? [])"
                                :key="peserta.id"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >
                                <!-- NOMOR -->
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ (pesertas.from ?? 1) + index }}
                                </td>

                                <!-- NAMA -->
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                            {{ peserta.nama?.charAt(0)?.toUpperCase() || '?' }}
                                        </div>

                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-white">
                                                {{ peserta.nama }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                ID #{{ peserta.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- USERNAME -->
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                        {{ peserta.username || '-' }}
                                    </span>
                                </td>

                                <!-- KAMAR -->
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span
                                        v-if="peserta.kamar"
                                        class="inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                                    >
                                        {{ peserta.kamar }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-sm italic text-slate-400"
                                    >
                                        -
                                    </span>
                                </td>

                                <!-- LEMBAGA -->
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ peserta.lembaga || '-' }}
                                </td>

                                <!-- ALAMAT -->
                                <td class="max-w-xs px-5 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <span
                                        v-if="peserta.alamat"
                                        class="line-clamp-2"
                                    >
                                        {{ peserta.alamat }}
                                    </span>

                                    <span
                                        v-else
                                        class="italic text-slate-400"
                                    >
                                        Tidak ada alamat
                                    </span>
                                </td>

                                <!-- STATUS -->
                                <td class="whitespace-nowrap px-5 py-4 text-center">
                                    <span
                                        v-if="peserta.is_active"
                                        class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"
                                    >
                                        Aktif
                                    </span>

                                    <span
                                        v-else
                                        class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-500/10 dark:text-red-400"
                                    >
                                        Nonaktif
                                    </span>
                                </td>

                                <!-- AKSI -->
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex justify-center gap-2">

                                        <Link
                                            :href="route('pesertas.show', peserta.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-indigo-500/30 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-400"
                                        >
                                            Lihat
                                        </Link>

                                        <Link
                                            :href="route('pesertas.edit', peserta.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-amber-500/30 dark:hover:bg-amber-500/10 dark:hover:text-amber-400"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            type="button"
                                            @click="deletePeserta(peserta)"
                                            class="rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20"
                                        >
                                            Hapus
                                        </button>

                                    </div>
                                </td>
                            </tr>

                            <!-- DATA KOSONG -->
                            <tr v-if="(pesertas.data ?? []).length === 0">
                                <td
                                    colspan="8"
                                    class="px-5 py-14 text-center"
                                >
                                    <div class="flex flex-col items-center justify-center">
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
                                                    stroke-width="1.8"
                                                    d="M17 20h5v-2a4 4 0 00-4-4h-1m-4 6H7v-2a4 4 0 014-4h2a4 4 0 014 4v2zm-3-10a4 4 0 11-8 0 4 4 0 018 0zm8 1a3 3 0 10-6 0"
                                                />
                                            </svg>
                                        </div>

                                        <p class="font-semibold text-slate-700 dark:text-slate-200">
                                            Tidak ada data peserta
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            {{
                                                filters.search
                                                    ? 'Tidak ditemukan peserta yang sesuai dengan pencarian.'
                                                    : 'Belum ada data peserta yang terdaftar.'
                                            }}
                                        </p>

                                        <button
                                            v-if="filters.search"
                                            type="button"
                                            @click="resetSearch"
                                            class="mt-4 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                        >
                                            Reset Pencarian
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div
                    v-if="pesertas.links?.length"
                    class="flex flex-col gap-4 border-t border-slate-200 px-5 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Menampilkan
                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                            {{ pesertas.from ?? 0 }}–{{ pesertas.to ?? 0 }}
                        </span>
                        dari
                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                            {{ pesertas.total ?? 0 }}
                        </span>
                        peserta
                    </p>

                    <div class="flex flex-wrap gap-1">
                        <template
                            v-for="(link, index) in pesertas.links"
                            :key="index"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                class="rounded-lg border px-3 py-2 text-sm transition"
                                :class="link.active
                                    ? 'border-indigo-600 bg-indigo-600 text-white'
                                    : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700'"
                            >
                                <span v-html="link.label"></span>
                            </Link>

                            <span
                                v-else
                                class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-400 dark:border-slate-700"
                            >
                                <span v-html="link.label"></span>
                            </span>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
