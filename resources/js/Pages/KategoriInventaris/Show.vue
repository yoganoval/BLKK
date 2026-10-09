<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    kategori: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <Head :title="`Detail Kategori - ${kategori.nama}`" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Detail Kategori
                </h1>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Informasi kategori inventaris
                </p>
            </div>

            <div class="flex gap-2">
                <Link
                    :href="route('kategori-inventaris.index')"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Kembali
                </Link>

                <Link
                    :href="route('kategori-inventaris.edit', kategori.id)"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700"
                >
                    Edit
                </Link>
            </div>
        </div>

        <!-- Informasi -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Nama Kategori
                    </label>

                    <p class="mt-1 text-gray-900 dark:text-white">
                        {{ kategori.nama }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Jumlah Inventaris
                    </label>

                    <p class="mt-1 text-gray-900 dark:text-white">
                        {{ kategori.inventaris?.length ?? 0 }} aset
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Keterangan
                    </label>

                    <p class="mt-1 text-gray-900 dark:text-white">
                        {{ kategori.keterangan || '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Daftar Inventaris -->
        <div
            class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Daftar Inventaris
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead
                        class="bg-gray-50 dark:bg-gray-900/50"
                    >
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                Kode
                            </th>

                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                Nama
                            </th>

                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                Jumlah
                            </th>

                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                Kondisi
                            </th>

                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">
                                Lokasi
                            </th>

                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr
                            v-for="item in kategori.inventaris"
                            :key="item.id"
                        >
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ item.kode }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ item.nama }}
                            </td>

                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ item.jumlah }} {{ item.satuan }}
                            </td>

                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ item.kondisi.replace('_', ' ') }}
                            </td>

                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ item.lokasi || '-' }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="route('inventaris.show', item.id)"
                                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400"
                                >
                                    Lihat
                                </Link>
                            </td>
                        </tr>

                        <tr v-if="!kategori.inventaris?.length">
                            <td
                                colspan="6"
                                class="px-6 py-10 text-center text-gray-500 dark:text-gray-400"
                            >
                                Belum ada inventaris dalam kategori ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
