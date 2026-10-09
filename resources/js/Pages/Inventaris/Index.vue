<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    inventaris: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
        }),
    },
})

const search = ref(props.filters.search ?? '')

/*
|--------------------------------------------------------------------------
| FORMAT
|--------------------------------------------------------------------------
*/

const formatKondisi = (kondisi) => {
    const labels = {
        baik: 'Baik',
        rusak_ringan: 'Rusak Ringan',
        rusak_berat: 'Rusak Berat',
    }

    return labels[kondisi] ?? kondisi
}

const kondisiClass = (kondisi) => {
    const classes = {
        baik: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        rusak_ringan: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
        rusak_berat: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    }

    return classes[kondisi] ??
        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
}

/*
|--------------------------------------------------------------------------
| FOTO
|--------------------------------------------------------------------------
*/

const fotoUrl = (item) => {
    const foto = item.files?.find(
        (file) => file.jenis === 'foto'
    )

    return foto?.file_path
        ? `/storage/${foto.file_path}`
        : null
}

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

const submitSearch = () => {
    router.get(
        route('inventaris.index'),
        {
            search: search.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| HAPUS
|--------------------------------------------------------------------------
*/

const hapus = (item) => {
    if (
        confirm(
            `Yakin ingin menghapus inventaris "${item.nama}"?`
        )
    ) {
        router.delete(
            route('inventaris.destroy', item.id)
        )
    }
}
</script>

<template>
    <Head title="Inventaris" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-gray-900 dark:text-white"
                >
                    Inventaris
                </h1>

                <p
                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                >
                    Kelola data aset dan inventaris BLKK.
                </p>
            </div>

            <Link
                :href="route('inventaris.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
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

                Tambah Inventaris
            </Link>
        </div>

        <!-- CARD -->
        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >

            <!-- TOOLBAR -->
            <div
                class="border-b border-gray-200 p-4 dark:border-gray-700"
            >
                <form
                    @submit.prevent="submitSearch"
                    class="flex flex-col gap-3 sm:flex-row"
                >
                    <div class="relative flex-1">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.04 6.04a7.5 7.5 0 0 0 10.61 10.61Z"
                            />
                        </svg>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama atau kode inventaris..."
                            class="w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>

                    <button
                        type="submit"
                        class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600"
                    >
                        Cari
                    </button>
                </form>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                            >
                                Inventaris
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                            >
                                Kategori
                            </th>

                            <th
                                class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                            >
                                Jumlah
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                            >
                                Kondisi
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                            >
                                Lokasi
                            </th>

                            <th
                                class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        class="divide-y divide-gray-200 dark:divide-gray-700"
                    >

                        <!-- DATA -->
                        <tr
                            v-for="item in inventaris.data"
                            :key="item.id"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700/30"
                        >

                            <!-- INVENTARIS -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">

                                    <!-- FOTO -->
                                    <div
                                        class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-900"
                                    >
                                        <img
                                            v-if="fotoUrl(item)"
                                            :src="fotoUrl(item)"
                                            :alt="item.nama"
                                            class="h-full w-full object-cover"
                                        />

                                        <div
                                            v-else
                                            class="flex h-full w-full items-center justify-center bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-7 w-7"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M3 16l5-5 4 4 3-3 6 6"
                                                />

                                                <rect
                                                    width="18"
                                                    height="18"
                                                    x="3"
                                                    y="3"
                                                    rx="2"
                                                    ry="2"
                                                />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- NAMA -->
                                    <div class="min-w-0">
                                        <div
                                            class="truncate font-semibold text-gray-900 dark:text-white"
                                        >
                                            {{ item.nama }}
                                        </div>

                                        <div
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ item.kode }}
                                        </div>
                                    </div>

                                </div>
                            </td>

                            <!-- KATEGORI -->
                            <td
                                class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ item.kategori?.nama ?? '-' }}
                            </td>

                            <!-- JUMLAH -->
                            <td
                                class="px-6 py-4 text-center text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ item.jumlah }}
                                {{ item.satuan }}
                            </td>

                            <!-- KONDISI -->
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="kondisiClass(item.kondisi)"
                                >
                                    {{ formatKondisi(item.kondisi) }}
                                </span>
                            </td>

                            <!-- LOKASI -->
                            <td
                                class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ item.lokasi ?? '-' }}
                            </td>

                            <!-- AKSI -->
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">

                                    <Link
                                        :href="route('inventaris.show', item.id)"
                                        class="rounded-lg px-2.5 py-2 text-sm text-gray-500 transition hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-blue-400"
                                    >
                                        Detail
                                    </Link>

                                    <Link
                                        :href="route('inventaris.edit', item.id)"
                                        class="rounded-lg px-2.5 py-2 text-sm text-gray-500 transition hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-blue-400"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        @click="hapus(item)"
                                        class="rounded-lg px-2.5 py-2 text-sm text-gray-500 transition hover:bg-red-50 hover:text-red-600 dark:text-gray-400 dark:hover:bg-red-900/20 dark:hover:text-red-400"
                                    >
                                        Hapus
                                    </button>

                                </div>
                            </td>

                        </tr>

                        <!-- EMPTY -->
                        <tr v-if="!inventaris.data?.length">
                            <td
                                colspan="6"
                                class="px-6 py-12 text-center"
                            >
                                <div class="text-4xl">
                                    📦
                                </div>

                                <p
                                    class="mt-3 font-semibold text-gray-900 dark:text-white"
                                >
                                    Belum ada inventaris
                                </p>

                                <p
                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Tambahkan aset pertama BLKK.
                                </p>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div
                v-if="inventaris.links?.length > 3"
                class="flex flex-wrap items-center justify-center gap-1 border-t border-gray-200 p-4 dark:border-gray-700"
            >
                <template
                    v-for="(link, index) in inventaris.links"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded-lg px-3 py-2 text-sm transition"
                        :class="
                            link.active
                                ? 'bg-indigo-600 text-white'
                                : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                        "
                    />

                    <span
                        v-else
                        v-html="link.label"
                        class="rounded-lg px-3 py-2 text-sm text-gray-400"
                    />
                </template>
            </div>

        </div>
    </div>
</template>
