<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps({
    soal: { type: Object, required: true },
    opsiJawaban: { type: Array, default: () => [] },
})

function hapusOpsi(soal, opsi) {
    if (confirm('Yakin ingin menghapus opsi jawaban ini?')) {
        router.delete(route('soal.opsi-jawaban.destroy', {
            soal: soal.id,
            opsi_jawaban: opsi.id,
        }))
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 dark:bg-gray-950">
        <div class="mx-auto max-w-5xl space-y-6">
            <Link :href="route('kuis.soal.index', soal.kuis_id)" class="text-sm text-blue-600 hover:underline">
                ← Kembali ke daftar soal
            </Link>

            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-xs font-semibold uppercase text-blue-600">Pertanyaan</p>
                <h1 class="mt-2 whitespace-pre-line text-xl font-bold dark:text-white">{{ soal.pertanyaan }}</h1>
                <p class="mt-2 text-sm text-gray-500">Kuis: {{ soal.kuis?.judul ?? '-' }}</p>
            </div>

            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <h2 class="text-xl font-bold dark:text-white">Opsi Jawaban</h2>

                <Link :href="route('soal.opsi-jawaban.create', soal.id)" class="rounded-lg bg-blue-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-blue-700">
                    + Tambah Opsi
                </Link>
            </div>

            <div v-if="opsiJawaban.length" class="space-y-3">
                <div v-for="opsi in opsiJawaban" :key="opsi.id" class="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-white p-4 sm:flex-row sm:items-center dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 font-bold dark:bg-gray-800 dark:text-white">
                            {{ opsi.opsi }}
                        </span>

                        <div>
                            <p class="whitespace-pre-line font-medium dark:text-white">{{ opsi.teks }}</p>
                            <span v-if="opsi.is_correct" class="mt-2 inline-block rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                Jawaban benar
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <Link :href="route('soal.opsi-jawaban.edit', [soal.id, opsi.id])" class="text-sm font-medium text-amber-600 hover:underline">
                            Edit
                        </Link>
                        <button @click="hapusOpsi(soal, opsi)" class="text-sm font-medium text-red-600 hover:underline">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="rounded-xl border border-dashed border-gray-300 p-10 text-center text-sm text-gray-500 dark:border-gray-700">
                Belum ada opsi jawaban untuk soal ini.
            </div>
        </div>
    </div>
</template>