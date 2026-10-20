<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Soal {
    id: number
    pertanyaan: string
    kuis_id?: number
}

interface OpsiJawaban {
    id: number
    soal_id: number
    opsi: string
    teks: string
    is_correct: boolean | number
    // urutan?: number
    soal?: Soal
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedOpsiJawaban {
    data: OpsiJawaban[]
    links: PaginationLink[]
    current_page: number
    total: number
}

defineProps<{
    opsiJawaban: PaginatedOpsiJawaban
}>()
</script>

<template>
    <Head title="Daftar Opsi Jawaban" />

    <div class="space-y-6 p-4 sm:p-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Opsi Jawaban Kuis
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Lihat opsi jawaban yang terhubung dengan setiap soal.
                </p>
            </div>

            <Link
                href="/soal-kuis"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
            >
                Kembali ke Soal
            </Link>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Total opsi jawaban:
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ opsiJawaban.total }}
                    </span>
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-5 py-4">No.</th>
                            <th class="px-5 py-4">Kode opsi</th>
                            <th class="px-5 py-4">Teks jawaban</th>
                            <th class="px-5 py-4">Soal</th>
                            <th class="px-5 py-4">Kunci jawaban</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr
                            v-for="(item, index) in opsiJawaban.data"
                            :key="item.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        >
                            <td class="px-5 py-4 text-gray-500">
                                {{ (opsiJawaban.current_page - 1) * 10 + index + 1 }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="rounded-md bg-gray-100 px-2.5 py-1 font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                    {{ item.opsi }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-gray-800 dark:text-gray-100">
                                {{ item.teks }}
                            </td>

                            <td class="max-w-sm px-5 py-4 text-gray-600 dark:text-gray-300">
                                {{ item.soal?.pertanyaan ?? `Soal #${item.soal_id}` }}
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    v-if="Boolean(item.is_correct)"
                                    class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-300"
                                >
                                    Benar
                                </span>
                                <span
                                    v-else
                                    class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    Salah
                                </span>
                            </td>
                        </tr>

                        <tr v-if="opsiJawaban.data.length === 0">
                            <td colspan="5" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
                                Belum ada opsi jawaban.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="opsiJawaban.links.length > 3"
                class="flex flex-wrap justify-center gap-1 border-t border-gray-200 p-4 dark:border-gray-700"
            >
                <template v-for="(link, index) in opsiJawaban.links" :key="index">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded-lg border px-3 py-2 text-sm"
                        :class="link.active
                            ? 'border-blue-600 bg-blue-600 text-white'
                            : 'border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300'"
                    />
                    <span
                        v-else
                        v-html="link.label"
                        class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-400 dark:border-gray-700"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
