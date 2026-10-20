<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps({
    kuis: { type: Object, required: true },
    soal: { type: Object, required: true },
})
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 dark:bg-gray-950">
        <div class="mx-auto max-w-3xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <Link :href="route('kuis.soal.index', kuis.id)" class="text-sm text-blue-600 hover:underline">
                ← Kembali
            </Link>

            <h1 class="mt-5 text-2xl font-bold dark:text-white">Detail Soal</h1>
            <p class="mt-1 text-sm text-gray-500">Kuis: {{ kuis.judul }}</p>

            <div class="mt-6 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                <p class="whitespace-pre-line font-medium dark:text-white">{{ soal.pertanyaan }}</p>
                <p class="mt-3 text-sm text-gray-500">Tipe: {{ soal.tipe }}</p>
                <p class="text-sm text-gray-500">Bobot: {{ soal.bobot }}</p>
            </div>

            <h2 class="mt-6 font-semibold dark:text-white">Opsi Jawaban</h2>
            <div class="mt-3 space-y-2">
                <div v-for="opsi in soal.opsi_jawaban" :key="opsi.id" class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                    <span class="font-semibold dark:text-white">{{ opsi.opsi }}.</span>
                    <span class="dark:text-gray-300"> {{ opsi.teks }}</span>
                </div>
            </div>
        </div>
    </div>
</template>