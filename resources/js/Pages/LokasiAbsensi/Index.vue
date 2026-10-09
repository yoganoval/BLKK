<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    lokasi: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')
const statusFilter = ref('semua')

const filteredLokasi = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    return props.lokasi.filter((item) => {
        const matchesSearch =
            !keyword ||
            item.nama?.toLowerCase().includes(keyword) ||
            String(item.latitude).includes(keyword) ||
            String(item.longitude).includes(keyword) ||
            item.keterangan?.toLowerCase().includes(keyword)

        const matchesStatus =
            statusFilter.value === 'semua' ||
            item.status === statusFilter.value

        return matchesSearch && matchesStatus
    })
})

const totalLokasi = computed(() => props.lokasi.length)

const lokasiAktif = computed(() =>
    props.lokasi.filter(
        item => item.status === 'aktif'
    ).length
)

const lokasiNonaktif = computed(() =>
    props.lokasi.filter(
        item => item.status === 'nonaktif'
    ).length
)

const formatCoordinate = (value) => {
    if (value === null || value === undefined) {
        return '-'
    }

    return Number(value).toFixed(7)
}

const confirmDelete = (item) => {
    if (
        !confirm(
            `Yakin ingin menghapus lokasi "${item.nama}"?`
        )
    ) {
        return
    }

    router.delete(
        route('lokasi-absensi.destroy', item.id)
    )
}
</script>

<template>
    <Head title="Lokasi Absensi" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                    Lokasi Absensi
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola lokasi dan radius yang digunakan untuk absensi staff.
                </p>
            </div>

            <Link
                :href="route('lokasi-absensi.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl
                       bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white
                       transition hover:bg-indigo-700"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Tambah Lokasi
            </Link>
        </div>

        <!-- STATISTIK -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm
                       dark:border-slate-700 dark:bg-slate-800"
            >
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Total Lokasi
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-white">
                    {{ totalLokasi }}
                </p>
            </div>

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm
                       dark:border-slate-700 dark:bg-slate-800"
            >
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Lokasi Aktif
                </p>

                <p class="mt-2 text-2xl font-bold text-emerald-600">
                    {{ lokasiAktif }}
                </p>
            </div>

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm
                       dark:border-slate-700 dark:bg-slate-800"
            >
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Lokasi Nonaktif
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-500">
                    {{ lokasiNonaktif }}
                </p>
            </div>

        </div>

        <!-- FILTER -->
        <div
            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm
                   dark:border-slate-700 dark:bg-slate-800"
        >
            <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_180px]">

                <div class="relative">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"
                        />
                    </svg>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari lokasi..."
                        class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-10
                               pr-4 text-sm text-slate-700 outline-none transition
                               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
                               dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                    />
                </div>

                <select
                    v-model="statusFilter"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm
                           text-slate-700 outline-none transition
                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
                           dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                >
                    <option value="semua">
                        Semua Status
                    </option>

                    <option value="aktif">
                        Aktif
                    </option>

                    <option value="nonaktif">
                        Nonaktif
                    </option>
                </select>

            </div>
        </div>

        <!-- TABLE -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                   dark:border-slate-700 dark:bg-slate-800"
        >
            <div class="overflow-x-auto">

                <table class="min-w-full">
                    <thead
                        class="border-b border-slate-200 bg-slate-50
                               dark:border-slate-700 dark:bg-slate-900/50"
                    >
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                #
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Nama Lokasi
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Koordinat
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Radius
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">

                        <tr
                            v-for="(item, index) in filteredLokasi"
                            :key="item.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-slate-700/30"
                        >
                            <td class="px-5 py-4 text-sm text-slate-500">
                                {{ index + 1 }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-800 dark:text-white">
                                    {{ item.nama }}
                                </div>

                                <div
                                    v-if="item.keterangan"
                                    class="mt-1 max-w-xs truncate text-xs text-slate-400"
                                >
                                    {{ item.keterangan }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-mono text-xs text-slate-600 dark:text-slate-300">
                                    {{ formatCoordinate(item.latitude) }}
                                </div>

                                <div class="font-mono text-xs text-slate-400">
                                    {{ formatCoordinate(item.longitude) }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">
                                    {{ item.radius }}
                                </span>

                                <span class="ml-1 text-xs text-slate-400">
                                    meter
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    v-if="item.status === 'aktif'"
                                    class="inline-flex items-center gap-2 rounded-full
                                           bg-emerald-50 px-3 py-1.5 text-xs font-semibold
                                           text-emerald-700
                                           dark:bg-emerald-500/10 dark:text-emerald-400"
                                >
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>

                                <span
                                    v-else
                                    class="inline-flex items-center gap-2 rounded-full
                                           bg-slate-100 px-3 py-1.5 text-xs font-semibold
                                           text-slate-600
                                           dark:bg-slate-700 dark:text-slate-300"
                                >
                                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                    Nonaktif
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-center gap-2">

                                    <Link
                                        :href="route('lokasi-absensi.show', item.id)"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-2
                                               text-xs font-semibold text-slate-700 transition
                                               hover:bg-slate-50 hover:text-indigo-600
                                               dark:border-slate-700 dark:bg-slate-800
                                               dark:text-slate-300 dark:hover:bg-slate-700"
                                    >
                                        Detail
                                    </Link>

                                    <Link
                                        :href="route('lokasi-absensi.edit', item.id)"
                                        class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2
                                               text-xs font-semibold text-indigo-600 transition
                                               hover:bg-indigo-100
                                               dark:border-indigo-500/20 dark:bg-indigo-500/10
                                               dark:text-indigo-400"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        @click="confirmDelete(item)"
                                        class="rounded-lg border border-red-200 bg-red-50 px-3 py-2
                                               text-xs font-semibold text-red-600 transition
                                               hover:bg-red-100
                                               dark:border-red-500/20 dark:bg-red-500/10
                                               dark:text-red-400"
                                    >
                                        Hapus
                                    </button>

                                </div>
                            </td>
                        </tr>

                        <!-- EMPTY -->
                        <tr v-if="filteredLokasi.length === 0">
                            <td
                                colspan="6"
                                class="px-5 py-12 text-center"
                            >
                                <div class="flex flex-col items-center">

                                    <div
                                        class="flex h-14 w-14 items-center justify-center rounded-full
                                               bg-slate-100 text-slate-400
                                               dark:bg-slate-700"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-7 w-7"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 21s7-4.35 7-10a7 7 0 10-14 0c0 5.65 7 10 7 10z"
                                            />

                                            <circle
                                                cx="12"
                                                cy="11"
                                                r="2.5"
                                            />
                                        </svg>
                                    </div>

                                    <h3 class="mt-4 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                        Tidak ada lokasi
                                    </h3>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Belum ada lokasi yang sesuai dengan pencarian.
                                    </p>

                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>

            <!-- FOOTER -->
            <div
                class="border-t border-slate-200 px-5 py-4 text-sm text-slate-500
                       dark:border-slate-700 dark:text-slate-400"
            >
                Menampilkan
                <span class="font-semibold text-slate-700 dark:text-slate-200">
                    {{ filteredLokasi.length }}
                </span>
                dari
                <span class="font-semibold text-slate-700 dark:text-slate-200">
                    {{ totalLokasi }}
                </span>
                lokasi
            </div>
        </div>

    </div>
</template>