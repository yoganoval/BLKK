<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps({
    soal: { type: Object, required: true },
    opsiJawaban: { type: Object, required: true },
})
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 dark:bg-gray-950">
        <div class="mx-auto max-w-3xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <Link :href="route('soal.opsi-jawaban.index', soal.id)" class="text-sm text-blue-600 hover:underline">
                ← Kembali ke opsi jawaban
            </Link>

            <h1 class="mt-5 text-2xl font-bold dark:text-white">Detail Opsi Jawaban</h1>

            <div class="mt-6 space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Pertanyaan</p>
                    <p class="mt-1 whitespace-pre-line font-medium dark:text-white">{{ soal.pertanyaan }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Kode Opsi</p>
                    <p class="mt-1 font-semibold dark:text-white">{{ opsiJawaban.opsi }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Teks Jawaban</p>
                    <p class="mt-1 whitespace-pre-line dark:text-white">{{ opsiJawaban.teks }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Status Jawaban</p>
                    <p class="mt-1 font-semibold" :class="opsiJawaban.is_correct ? 'text-green-600' : 'text-gray-500'">
                        {{ opsiJawaban.is_correct ? 'Jawaban benar' : 'Jawaban salah' }}
                    </p>
                </div>
            </div>

            <Link :href="route('soal.opsi-jawaban.edit', [soal.id, opsiJawaban.id])" class="mt-6 inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                Edit Opsi
            </Link>
        </div>
    </div>
</template>