<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

defineProps({
    kategori: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },
})

const hapus = (item) => {
    if (
        !confirm(
            `Apakah Anda yakin ingin menghapus kategori "${item.nama}"?`
        )
    ) {
        return
    }

    router.delete(
        route('kategori-inventaris.destroy', item.id),
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>
    <Head title="Kategori Inventaris" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Kategori Inventaris
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola kategori aset inventaris BLKK.
                </p>
            </div>

            <Link
                :href="route('kategori-inventaris.create')"
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

                Tambah Kategori
            </Link>
        </div>

        <!-- TABLE -->
        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div
                class="border-b border-gray-200 px-5 py-4 dark:border-gray-700"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/10"
                    >
                        <svg
                            class="h-5 w-5 text-indigo-600 dark:text-indigo-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Daftar Kategori
                        </h2>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Kategori yang digunakan untuk mengelompokkan aset.
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900/50 dark:text-gray-400"
                    >
                        <tr>
                            <th class="px-5 py-3">
                                #
                            </th>

                            <th class="px-5 py-3">
                                Nama Kategori
                            </th>

                            <th class="px-5 py-3">
                                Keterangan
                            </th>

                            <th class="px-5 py-3 text-center">
                                Jumlah Aset
                            </th>

                            <th class="px-5 py-3 text-right">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        v-if="kategori.data?.length"
                        class="divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="(item, index) in kategori.data"
                            :key="item.id"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700/30"
                        >
                            <td class="whitespace-nowrap px-5 py-4 text-gray-500 dark:text-gray-400">
                                {{ (kategori.current_page - 1) * kategori.per_page + index + 1 }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/10"
                                    >
                                        <svg
                                            class="h-5 w-5 text-indigo-600 dark:text-indigo-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"
                                            />
                                        </svg>
                                    </div>

                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ item.nama }}
                                        </p>

                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            ID #{{ item.id }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="max-w-md px-5 py-4">
                                <p
                                    class="line-clamp-2 text-gray-600 dark:text-gray-300"
                                >
                                    {{ item.keterangan || '-' }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span
                                    class="inline-flex min-w-10 items-center justify-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400"
                                >
                                    {{ item.inventaris_count ?? 0 }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">

                                    <Link
                                        :href="route('kategori-inventaris.show', item.id)"
                                        class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-indigo-400"
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
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />
                                        </svg>
                                    </Link>

                                    <Link
                                        :href="route('kategori-inventaris.edit', item.id)"
                                        class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-indigo-400"
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
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                            />
                                        </svg>
                                    </Link>

                                    <button
                                        type="button"
                                        class="rounded-lg p-2 text-gray-500 transition hover:bg-red-50 hover:text-red-600 dark:text-gray-400 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                                        title="Hapus"
                                        @click="hapus(item)"
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
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <tbody v-else>
                        <tr>
                            <td
                                colspan="5"
                                class="px-5 py-14 text-center"
                            >
                                <div class="flex flex-col items-center">
                                    <div
                                        class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700"
                                    >
                                        <svg
                                            class="h-7 w-7 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10l8-4"
                                            />
                                        </svg>
                                    </div>

                                    <p class="font-semibold text-gray-700 dark:text-gray-200">
                                        Belum ada kategori
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Silakan tambahkan kategori inventaris.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div
                v-if="kategori.links?.length > 3"
                class="border-t border-gray-200 px-5 py-4 dark:border-gray-700"
            >
                <div class="flex flex-wrap gap-1">
                    <template
                        v-for="(link, index) in kategori.links"
                        :key="index"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            class="rounded-lg px-3 py-2 text-sm transition"
                            :class="
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                            "
                        >
                            <span v-html="link.label" />
                        </Link>

                        <span
                            v-else
                            class="rounded-lg px-3 py-2 text-sm text-gray-400 dark:text-gray-600"
                        >
                            <span v-html="link.label" />
                        </span>
                    </template>
                </div>
            </div>
        </div>

    </div>
</template>