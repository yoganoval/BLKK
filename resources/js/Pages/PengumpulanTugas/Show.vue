<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

interface Pengumpulan {
    id: number
    jawaban: string | null
    file_path: string | null
    dikumpulkan_at: string | null
    nilai: number | string | null
    feedback: string | null
    status: string
    tugas?: { id: number; judul: string } | null
    peserta?: { id: number; nama: string } | null
    penilai?: { id: number; name: string } | null
}

const props = defineProps<{
    pengumpulanTugas: Pengumpulan
}>()

const statusLabel: Record<string, string> = {
    belum_dikumpulkan: 'Belum Dikumpulkan',
    dikumpulkan: 'Dikumpulkan',
    dinilai: 'Dinilai',
    terlambat: 'Terlambat',
}
</script>

<template>
    <Head title="Detail Pengumpulan Tugas" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-6">
                <Link
                    :href="route('pengumpulan-tugas.index')"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
                >
                    ← Kembali
                </Link>

                <h1 class="mt-4 text-xl font-bold text-slate-800 dark:text-white">
                    Detail Pengumpulan Tugas
                </h1>
            </div>

            <div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div>
                    <p class="text-sm text-slate-500">Peserta</p>
                    <p class="font-semibold text-slate-800 dark:text-white">
                        {{ props.pengumpulanTugas.peserta?.nama ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Tugas</p>
                    <p class="font-semibold text-slate-800 dark:text-white">
                        {{ props.pengumpulanTugas.tugas?.judul ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Waktu Pengumpulan</p>
                    <p class="text-slate-800 dark:text-white">
                        {{ props.pengumpulanTugas.dikumpulkan_at ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Jawaban</p>
                    <p class="whitespace-pre-wrap text-slate-800 dark:text-white">
                        {{ props.pengumpulanTugas.jawaban || 'Tidak ada jawaban teks.' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">File Jawaban</p>
                    <a
                        v-if="props.pengumpulanTugas.file_path"
                        :href="`/storage/${props.pengumpulanTugas.file_path}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-indigo-600 hover:underline"
                    >
                        Buka File Jawaban
                    </a>
                    <p v-else class="text-slate-500">Tidak ada file.</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Nilai</p>
                    <p class="font-semibold text-slate-800 dark:text-white">
                        {{ props.pengumpulanTugas.nilai ?? 'Belum dinilai' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Umpan Balik</p>
                    <p class="whitespace-pre-wrap text-slate-800 dark:text-white">
                        {{ props.pengumpulanTugas.feedback || '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Status</p>
                    <p class="font-semibold text-slate-800 dark:text-white">
                        {{ statusLabel[props.pengumpulanTugas.status] ?? props.pengumpulanTugas.status }}
                    </p>
                </div>

                <div v-if="props.pengumpulanTugas.penilai">
                    <p class="text-sm text-slate-500">Dinilai Oleh</p>
                    <p class="text-slate-800 dark:text-white">
                        {{ props.pengumpulanTugas.penilai.name }}
                    </p>
                </div>

                <div class="border-t border-slate-200 pt-5 dark:border-slate-800">
                    <Link
                        :href="route('pengumpulan-tugas.edit', {
                            pengumpulan_tuga: props.pengumpulanTugas.id,
                        })"
                        class="inline-flex rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Beri Penilaian
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
