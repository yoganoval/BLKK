<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    ArrowLeft,
    Pencil,
    FileText,
    Download,
    UserRound,
    CalendarClock,
    Award,
    MessageSquare,
} from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    pengumpulanTugas: { type: Object, required: true },
})

const item = computed(() => props.pengumpulanTugas)

const statusLabels = {
    belum_dikumpulkan: 'Belum dikumpulkan',
    dikumpulkan: 'Dikumpulkan',
    dinilai: 'Sudah dinilai',
    terlambat: 'Terlambat',
}

const statusClasses = {
    belum_dikumpulkan: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    dikumpulkan: 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
    dinilai: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
    terlambat: 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300',
}

function formatDate(value) {
    if (!value) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'long',
        timeStyle: 'short',
    }).format(new Date(value))
}

function fileUrl(path) {
    if (!path) return '#'
    return `/storage/${path.split('/').map(encodeURIComponent).join('/')}`
}
</script>

<template>
    <Head title="Detail Pengumpulan Tugas" />

    <div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <Link
                :href="route('pengumpulan-tugas.index')"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600"
            >
                <ArrowLeft class="size-4" />
                Kembali
            </Link>

            <Link
                :href="route('pengumpulan-tugas.edit', item.id)"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
            >
                <Pencil class="size-4" />
                Beri Penilaian
            </Link>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-7 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                <div>
                    <p class="text-sm font-medium text-blue-600">Detail Pengumpulan</p>
                    <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">
                        {{ item.tugas?.judul ?? 'Tugas tidak ditemukan' }}
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        ID pengumpulan: #{{ item.id }}
                    </p>
                </div>

                <span
                    :class="[
                        'inline-flex w-fit rounded-full px-3 py-1.5 text-xs font-semibold',
                        statusClasses[item.status] ?? statusClasses.belum_dikumpulkan,
                    ]"
                >
                    {{ statusLabels[item.status] ?? item.status }}
                </span>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-950">
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <UserRound class="size-4" />
                        Peserta
                    </div>
                    <p class="mt-2 font-semibold text-slate-900 dark:text-white">
                        {{ item.peserta?.nama ?? '-' }}
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-950">
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <CalendarClock class="size-4" />
                        Waktu Pengumpulan
                    </div>
                    <p class="mt-2 font-semibold text-slate-900 dark:text-white">
                        {{ formatDate(item.dikumpulkan_at) }}
                    </p>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-7 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-2">
                <FileText class="size-5 text-blue-600" />
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    Jawaban Peserta
                </h2>
            </div>

            <div class="mt-4 whitespace-pre-wrap break-words rounded-xl bg-slate-50 p-4 text-sm leading-7 text-slate-700 dark:bg-slate-950 dark:text-slate-300">
                {{ item.jawaban || 'Peserta tidak mengisi jawaban berbentuk teks.' }}
            </div>

            <div v-if="item.file_path" class="mt-4 flex flex-col gap-3 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center dark:border-slate-700">
                <FileText class="size-8 shrink-0 text-blue-600" />
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">
                        Lampiran tugas
                    </p>
                    <p class="mt-1 break-all text-xs text-slate-500">
                        {{ item.file_path }}
                    </p>
                </div>
                <a
                    :href="fileUrl(item.file_path)"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                >
                    <Download class="size-4" />
                    Buka File
                </a>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-7 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-2">
                <Award class="size-5 text-emerald-600" />
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    Hasil Penilaian
                </h2>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 p-5 dark:border-slate-700">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Nilai</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                        {{ item.nilai !== null ? `${item.nilai}/100` : 'Belum dinilai' }}
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 p-5 dark:border-slate-700">
                    <p class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <UserRound class="size-4" />
                        Dinilai oleh
                    </p>
                    <p class="mt-2 font-semibold text-slate-900 dark:text-white">
                        {{ item.penilai?.name ?? '-' }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ formatDate(item.dinilai_at) }}
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <h3 class="flex items-center gap-2 font-semibold text-slate-900 dark:text-white">
                    <MessageSquare class="size-4" />
                    Feedback Instruktur
                </h3>
                <p class="mt-3 whitespace-pre-wrap break-words rounded-xl bg-slate-50 p-4 text-sm leading-7 text-slate-700 dark:bg-slate-950 dark:text-slate-300">
                    {{ item.feedback || 'Belum ada feedback.' }}
                </p>
            </div>
        </section>
    </div>
</template>
