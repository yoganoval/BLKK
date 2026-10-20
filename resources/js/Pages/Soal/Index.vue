
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Kuis {
    id: number
    judul: string
}

interface Soal {
    id: number
    kuis_id: number
    pertanyaan: string
    tipe?: string
    kuis?: Kuis
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedSoal {
    data: Soal[]
    links: PaginationLink[]
    current_page: number
    per_page: number
    last_page: number
    total: number
}

defineProps<{
    soals: PaginatedSoal
}>()
</script>

<template>
    <Head title="Daftar Soal Kuis" />

    <div class="space-y-6 p-4 sm:p-6">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Daftar Soal Kuis
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola dan tambahkan soal pada kuis yang tersedia.
                </p>
            </div>

            <Link
                href="/kuis"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
            >
                <span>+</span>
                Tambah Soal
            </Link>
        </div>

        <!-- Daftar Soal -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <div>
                    <h2 class="font-semibold text-gray-800 dark:text-white">
                        Data Soal
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total {{ soals.total }} soal
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-5 py-4">No.</th>
                            <th class="px-5 py-4">Pertanyaan</th>
                            <th class="px-5 py-4">Kuis</th>
                            <th class="px-5 py-4">Tipe</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr
                            v-for="(soal, index) in soals.data"
                            :key="soal.id"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        >
                            <td class="whitespace-nowrap px-5 py-4 text-gray-500 dark:text-gray-400">
                                {{ (soals.current_page - 1) * (soals.per_page || 10) + index + 1 }}
                            </td>

                            <td class="min-w-64 max-w-md px-5 py-4 font-medium text-gray-800 dark:text-gray-100">
                                {{ soal.pertanyaan }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-gray-600 dark:text-gray-300">
                                {{ soal.kuis?.judul ?? `Kuis #${soal.kuis_id}` }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="soal.tipe === 'pilihan_ganda'
                                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'"
                                >
                                    {{ soal.tipe === 'pilihan_ganda'
                                        ? 'Pilihan Ganda'
                                        : soal.tipe === 'benar_salah'
                                            ? 'Benar / Salah'
                                            : soal.tipe ?? '-' }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="`/kuis/${soal.kuis_id}/soal`"
                                        class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                                    >
                                        Kelola Soal
                                    </Link>

                                    <Link
                                        :href="`/kuis/${soal.kuis_id}/soal/create`"
                                        class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
                                    >
                                        + Tambah
                                    </Link>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="soals.data.length === 0">
                            <td
                                colspan="5"
                                class="px-5 py-12 text-center"
                            >
                                <div class="text-gray-500 dark:text-gray-400">
                                    <p class="font-medium">Belum ada soal kuis.</p>
                                    <p class="mt-1 text-sm">
                                        Pilih kuis terlebih dahulu untuk mulai menambahkan soal.
                                    </p>
                                    <Link
                                        href="/kuis"
                                        class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                                    >
                                        Pilih Kuis
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="soals.links.length > 3"
                class="flex flex-wrap justify-center gap-1 border-t border-gray-200 p-4 dark:border-gray-700"
            >
                <template
                    v-for="(link, index) in soals.links"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded-lg border px-3 py-2 text-sm transition"
                        :class="link.active
                            ? 'border-blue-600 bg-blue-600 text-white'
                            : 'border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'"
                    />

                    <span
                        v-else
                        v-html="link.label"
                        class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-400 dark:border-gray-700"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
