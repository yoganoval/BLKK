<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import {
    ArrowLeft,
    Pencil,
    Award,
    User,
    GraduationCap,
    Percent,
    FileText,
} from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Nilai {
    id: number
    nilai: number
    catatan?: string | null
    peserta?: {
        id: number
        nama: string
    }
    komponen_nilai?: {
        id: number
        nama: string
        bobot: number
        nilai_maksimal: number
        kelas?: {
            id: number
            nama: string
            durasi_hari: number
        }
    }
    penilai?: {
        id: number
        name: string
    }
}

defineProps<{
    nilai: Nilai
}>()
</script>

<template>
    <Head title="Detail Nilai" />

    <div class="mx-auto max-w-3xl space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <Link
                    :href="route('nilai-peserta.index')"
                    class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    <ArrowLeft class="h-5 w-5" />
                </Link>

                <div>
                    <h1
                        class="text-2xl font-bold text-slate-900 dark:text-white"
                    >
                        Detail Nilai
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Informasi hasil penilaian peserta.
                    </p>
                </div>
            </div>

            <Link
                :href="
                    route(
                        'nilai-peserta.edit',
                        nilai.id
                    )
                "
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:opacity-90"
            >
                <Pencil class="h-4 w-4" />
                Edit Nilai
            </Link>
        </div>

        <!-- Nilai Utama -->
        <div
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="text-center">
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-primary/10"
                >
                    <span
                        class="text-3xl font-bold text-primary"
                    >
                        {{ nilai.nilai }}
                    </span>
                </div>

                <h2
                    class="mt-4 text-xl font-bold text-slate-900 dark:text-white"
                >
                    {{ nilai.komponen_nilai?.nama }}
                </h2>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Nilai maksimal:
                    {{ nilai.komponen_nilai?.nilai_maksimal }}
                </p>
            </div>
        </div>

        <!-- Informasi -->
        <div
            class="grid gap-4 md:grid-cols-2"
        >
            <div
                class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex items-center gap-3">
                    <User class="h-5 w-5 text-primary" />

                    <div>
                        <p class="text-xs text-slate-500">
                            Peserta
                        </p>

                        <p
                            class="font-semibold text-slate-900 dark:text-white"
                        >
                            {{ nilai.peserta?.nama }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex items-center gap-3">
                    <GraduationCap
                        class="h-5 w-5 text-primary"
                    />

                    <div>
                        <p class="text-xs text-slate-500">
                            Kelas
                        </p>

                        <p
                            class="font-semibold text-slate-900 dark:text-white"
                        >
                            {{
                                nilai.komponen_nilai?.kelas
                                    ?.nama
                            }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex items-center gap-3">
                    <Percent
                        class="h-5 w-5 text-primary"
                    />

                    <div>
                        <p class="text-xs text-slate-500">
                            Bobot
                        </p>

                        <p
                            class="font-semibold text-slate-900 dark:text-white"
                        >
                            {{ nilai.komponen_nilai?.bobot }}%
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex items-center gap-3">
                    <Award class="h-5 w-5 text-primary" />

                    <div>
                        <p class="text-xs text-slate-500">
                            Penilai
                        </p>

                        <p
                            class="font-semibold text-slate-900 dark:text-white"
                        >
                            {{ nilai.penilai?.name ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div
            class="rounded-xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="mb-3 flex items-center gap-2">
                <FileText class="h-5 w-5 text-primary" />

                <h2
                    class="font-semibold text-slate-900 dark:text-white"
                >
                    Catatan Penilaian
                </h2>
            </div>

            <p
                v-if="nilai.catatan"
                class="whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-slate-400"
            >
                {{ nilai.catatan }}
            </p>

            <p
                v-else
                class="text-sm italic text-slate-400"
            >
                Tidak ada catatan.
            </p>
        </div>
    </div>
</template>