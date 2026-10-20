<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

interface Percobaan {
    id: number
    percobaan_ke: number
    jumlah_esai: number
    esai_belum_dikoreksi: number
    peserta: { nama: string }
    kuis: { judul: string }
}

interface Pagination {
    data: Percobaan[]
    links: { url: string | null; label: string; active: boolean }[]
}

defineProps<{ percobaan: Pagination }>()

const bersihkanLabel = (label: string) =>
    label.replace('&laquo;', '«').replace('&raquo;', '»').replace(/<[^>]*>/g, '')
</script>

<template>
    <Head title="Koreksi Esai" />

    <div class="space-y-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Koreksi Jawaban Esai
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Periksa jawaban peserta dan berikan nilai secara manual.
            </p>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <tr>
                            <th class="p-4">Peserta</th>
                            <th class="p-4">Kuis</th>
                            <th class="p-4 text-center">Jumlah Esai</th>
                            <th class="p-4 text-center">Belum Dikoreksi</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr
                            v-for="item in percobaan.data"
                            :key="item.id"
                            class="text-gray-700 dark:text-gray-200"
                        >
                            <td class="p-4 font-medium">
                                {{ item.peserta?.nama ?? 'Peserta' }}
                            </td>
                            <td class="p-4">
                                {{ item.kuis?.judul ?? '-' }}
                                <div class="mt-1 text-xs text-gray-500">
                                    Percobaan ke-{{ item.percobaan_ke }}
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                {{ item.jumlah_esai }}
                            </td>
                            <td class="p-4 text-center">
                                <span
                                    :class="item.esai_belum_dikoreksi
                                        ? 'text-orange-600'
                                        : 'text-green-600'"
                                    class="font-semibold"
                                >
                                    {{ item.esai_belum_dikoreksi }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <Link
                                    :href="route('koreksi-esai.show', {
                                        percobaanKuis: item.id
                                    })"
                                    class="inline-block rounded-lg bg-blue-600 px-3 py-2 font-medium text-white hover:bg-blue-700"
                                >
                                    Koreksi
                                </Link>
                            </td>
                        </tr>

                        <tr v-if="percobaan.data.length === 0">
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                Belum ada jawaban esai yang perlu diperiksa.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-gray-200 p-4 dark:border-gray-700">
                <template v-for="link in percobaan.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="rounded-lg border px-3 py-2 text-sm"
                        :class="link.active
                            ? 'border-blue-600 bg-blue-600 text-white'
                            : 'border-gray-300 text-gray-700 dark:border-gray-600 dark:text-gray-200'"
                    >
                        {{ bersihkanLabel(link.label) }}
                    </Link>
                    <span
                        v-else
                        class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-400 dark:border-gray-700"
                    >
                        {{ bersihkanLabel(link.label) }}
                    </span>
                </template>
            </div>
        </div>
    </div>
</template>