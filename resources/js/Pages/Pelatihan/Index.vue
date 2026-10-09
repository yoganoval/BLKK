<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pelatihans: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')

const filteredPelatihans = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    if (!keyword) {
        return props.pelatihans
    }

    return props.pelatihans.filter((pelatihan) =>
        [
            pelatihan.nama,
            pelatihan.keterangan,
        ]
            .filter(Boolean)
            .some((value) =>
                String(value).toLowerCase().includes(keyword)
            )
    )
})

const hapusPelatihan = (pelatihan) => {
    if (
        !confirm(
            `Yakin ingin menghapus pelatihan "${pelatihan.nama}"?`
        )
    ) {
        return
    }

    router.delete(route('pelatihans.destroy', pelatihan.id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Pelatihan" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6 lg:p-8">

            <!-- ========================================= -->
            <!-- HEADER -->
            <!-- ========================================= -->
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                        >
                            <!-- Icon Graduation -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 14l9-5-9-5-9 5 9 5z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 14v7"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 12v4c0 1.5 3.1 3 7 3s7-1.5 7-3v-4"
                                />
                            </svg>
                        </div>

                        <div>
                            <h1
                                class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white"
                            >
                                Pelatihan
                            </h1>

                            <p
                                class="mt-0.5 text-sm text-slate-500 dark:text-slate-400"
                            >
                                Kelola data pelatihan yang tersedia
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Tambah -->
                <Link
                    v-if="$page.props.auth?.user"
                    :href="route('pelatihans.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950"
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

                    Tambah Pelatihan
                </Link>
            </div>

            <!-- ========================================= -->
            <!-- STAT CARD -->
            <!-- ========================================= -->
            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm font-medium text-slate-500 dark:text-slate-400"
                            >
                                Total Pelatihan
                            </p>

                            <p
                                class="mt-2 text-3xl font-bold text-slate-900 dark:text-white"
                            >
                                {{ pelatihans.length }}
                            </p>
                        </div>

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
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
                                    d="M12 14l9-5-9-5-9 5 9 5z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 14v7"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================= -->
            <!-- MAIN CARD -->
            <!-- ========================================= -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >

                <!-- TOOLBAR -->
                <div
                    class="flex flex-col gap-4 border-b border-slate-200 p-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h2
                            class="text-base font-semibold text-slate-900 dark:text-white"
                        >
                            Daftar Pelatihan
                        </h2>

                        <p
                            class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                        >
                            Kelola informasi pelatihan yang tersedia.
                        </p>
                    </div>

                    <!-- SEARCH -->
                    <div class="relative w-full sm:w-80">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-slate-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle
                                    cx="11"
                                    cy="11"
                                    r="7"
                                />
                                <path
                                    stroke-linecap="round"
                                    d="M20 20l-3.5-3.5"
                                />
                            </svg>
                        </div>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari pelatihan..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder:text-slate-500 dark:focus:border-indigo-500 dark:focus:bg-slate-800"
                        />
                    </div>
                </div>

                <!-- ========================================= -->
                <!-- TABLE -->
                <!-- ========================================= -->
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-200 bg-slate-50/80 dark:border-slate-800 dark:bg-slate-800/40"
                            >
                                <th
                                    class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    #
                                </th>

                                <th
                                    class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Nama Pelatihan
                                </th>

                                <th
                                    class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Keterangan
                                </th>

                                <th
                                    class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-slate-100 dark:divide-slate-800"
                        >
                            <!-- DATA -->
                            <tr
                                v-for="(pelatihan, index) in filteredPelatihans"
                                :key="pelatihan.id"
                                class="group transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >
                                <!-- NOMOR -->
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-500 dark:text-slate-400"
                                >
                                    {{ index + 1 }}
                                </td>

                                <!-- NAMA -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 font-bold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                                        >
                                            {{
                                                pelatihan.nama
                                                    ?.charAt(0)
                                                    ?.toUpperCase() ?? 'P'
                                            }}
                                        </div>

                                        <div class="min-w-0">
                                            <p
                                                class="truncate text-sm font-semibold text-slate-800 dark:text-white"
                                            >
                                                {{ pelatihan.nama }}
                                            </p>

                                            <p
                                                class="mt-0.5 text-xs text-slate-400"
                                            >
                                                ID #{{ pelatihan.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- KETERANGAN -->
                                <td class="max-w-md px-6 py-4">
                                    <p
                                        v-if="pelatihan.keterangan"
                                        class="line-clamp-2 text-sm text-slate-600 dark:text-slate-400"
                                    >
                                        {{ pelatihan.keterangan }}
                                    </p>

                                    <span
                                        v-else
                                        class="text-sm italic text-slate-400 dark:text-slate-500"
                                    >
                                        Tidak ada keterangan
                                    </span>
                                </td>

                                <!-- AKSI -->
                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-center gap-2"
                                    >
                                        <!-- DETAIL -->
                                        <Link
                                            :href="
                                                route(
                                                    'pelatihans.show',
                                                    pelatihan.id
                                                )
                                            "
                                            title="Lihat detail"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:border-indigo-500/30 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-400"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                />
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="3"
                                                />
                                            </svg>
                                        </Link>

                                        <!-- EDIT -->
                                        <Link
                                            :href="
                                                route(
                                                    'pelatihans.edit',
                                                    pelatihan.id
                                                )
                                            "
                                            title="Edit pelatihan"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:border-amber-500/30 dark:hover:bg-amber-500/10 dark:hover:text-amber-400"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                                                />
                                            </svg>
                                        </Link>

                                        <!-- DELETE -->
                                        <button
                                            type="button"
                                            title="Hapus pelatihan"
                                            @click="hapusPelatihan(pelatihan)"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:border-red-500/30 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-8 0h10"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- EMPTY -->
                            <tr v-if="filteredPelatihans.length === 0">
                                <td
                                    colspan="4"
                                    class="px-6 py-16 text-center"
                                >
                                    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-7 w-7"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 14l9-5-9-5-9 5 9 5z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 14v7"
                                            />
                                        </svg>
                                    </div>

                                    <h3
                                        class="mt-4 text-sm font-semibold text-slate-700 dark:text-slate-300"
                                    >
                                        {{
                                            search
                                                ? 'Pelatihan tidak ditemukan'
                                                : 'Belum ada data pelatihan'
                                        }}
                                    </h3>

                                    <p
                                        class="mt-1 text-xs text-slate-400 dark:text-slate-500"
                                    >
                                        {{
                                            search
                                                ? 'Coba gunakan kata kunci pencarian yang berbeda.'
                                                : 'Silakan tambahkan data pelatihan terlebih dahulu.'
                                        }}
                                    </p>

                                    <Link
                                        v-if="!search"
                                        :href="route('pelatihans.create')"
                                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-indigo-700"
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
                                                d="M12 4v16m8-8H4"
                                            />
                                        </svg>

                                        Tambah Pelatihan
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER -->
                <div
                    class="border-t border-slate-200 bg-slate-50/50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/20"
                >
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Menampilkan
                        <span
                            class="font-semibold text-slate-700 dark:text-slate-300"
                        >
                            {{ filteredPelatihans.length }}
                        </span>
                        dari
                        <span
                            class="font-semibold text-slate-700 dark:text-slate-300"
                        >
                            {{ pelatihans.length }}
                        </span>
                        pelatihan
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>