<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    kelass: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')

const filteredKelass = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    if (!keyword) {
        return props.kelass
    }

    return props.kelass.filter((kelas) => {
        return (
            kelas.nama?.toLowerCase().includes(keyword) ||
            String(kelas.durasi_hari ?? '').includes(keyword) ||
            kelas.keterangan?.toLowerCase().includes(keyword)
        )
    })
})

const deleteKelas = (kelas) => {
    if (!confirm(`Apakah Anda yakin ingin menghapus kelas "${kelas.nama}"?`)) {
        return
    }

    router.delete(route('kelas.destroy', kelas.id))
}
</script>

<template>
    <Head title="Kelas" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
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
                                d="M12 14l9-5-9-5-9 5 9 5z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 14l6.16-3.422A12.083 12.083 0 0118 14.5c0 2.485-2.686 4.5-6 4.5s-6-2.015-6-4.5c0-1.37.82-2.605 2.16-3.422L12 14z"
                            />
                        </svg>

                        <span>Master Data</span>
                        <span>/</span>
                        <span class="font-medium text-slate-700 dark:text-slate-200">
                            Kelas
                        </span>
                    </div>

                    <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                        Kelas
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Kelola jenis kelas dan durasi pelatihan.
                    </p>
                </div>

                <Link
                    :href="route('kelas.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950"
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

                    Tambah Kelas
                </Link>
            </div>

            <!-- SUMMARY -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Total Kelas
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                                {{ props.kelass.length }}
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
                                    d="M12 14l9-5-9-5-9 5 9 5z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 14l6.16-3.422A12.083 12.083 0 0118 14.5c0 2.485-2.686 4.5-6 4.5s-6-2.015-6-4.5c0-1.37.82-2.605 2.16-3.422L12 14z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Kelas Regular
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                        {{ props.kelass.filter(k => k.durasi_hari === 5).length }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Durasi 5 hari
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Kelas Private
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                        {{ props.kelass.filter(k => k.durasi_hari === 10).length }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Durasi 10 hari
                    </p>
                </div>
            </div>

            <!-- TABLE CARD -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <!-- SEARCH -->
                <div class="border-b border-slate-200 p-4 dark:border-slate-800 sm:p-5">
                    <div class="relative max-w-md">
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
                            type="text"
                            placeholder="Cari kelas..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        />
                    </div>
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
                                    Nama Kelas
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Durasi
                                </th>

                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Keterangan
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="(kelas, index) in filteredKelass"
                                :key="kelas.id"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ index + 1 }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
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
                                                    d="M12 14l9-5-9-5-9 5 9 5z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 14l6.16-3.422A12.083 12.083 0 0118 14.5c0 2.485-2.686 4.5-6 4.5s-6-2.015-6-4.5c0-1.37.82-2.605 2.16-3.422L12 14z"
                                                />
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-white">
                                                {{ kelas.nama }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                        {{ kelas.durasi_hari }} Hari
                                    </span>
                                </td>

                                <td class="max-w-xs px-5 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <span v-if="kelas.keterangan">
                                        {{ kelas.keterangan }}
                                    </span>

                                    <span v-else class="italic text-slate-400">
                                        Tidak ada keterangan
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex justify-center gap-2">

                                        <!-- SHOW -->
                                        <Link
                                            :href="route('kelas.show', kelas.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-indigo-500/30 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-400"
                                        >
                                            Lihat
                                        </Link>

                                        <!-- EDIT -->
                                        <Link
                                            :href="route('kelas.edit', kelas.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-amber-500/30 dark:hover:bg-amber-500/10 dark:hover:text-amber-400"
                                        >
                                            Edit
                                        </Link>

                                        <!-- DELETE -->
                                        <button
                                            type="button"
                                            @click="deleteKelas(kelas)"
                                            class="rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20"
                                        >
                                            Hapus
                                        </button>

                                    </div>
                                </td>
                            </tr>

                            <!-- EMPTY -->
                            <tr v-if="filteredKelass.length === 0">
                                <td colspan="5" class="px-5 py-14 text-center">
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
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0l-3 3m3-3l-3-3m-13 3l3 3m-3-3l3-3M8 21h8"
                                                />
                                            </svg>
                                        </div>

                                        <p class="font-semibold text-slate-700 dark:text-slate-200">
                                            Tidak ada data kelas
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            Belum ada kelas yang sesuai dengan pencarian.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER -->
                <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-800">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Menampilkan
                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                            {{ filteredKelass.length }}
                        </span>
                        dari
                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                            {{ props.kelass.length }}
                        </span>
                        kelas
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>