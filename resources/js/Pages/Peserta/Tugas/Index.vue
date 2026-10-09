<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import {
    ClipboardList,
    Clock,
    CheckCircle2,
    AlertCircle,
    ArrowRight,
} from 'lucide-vue-next'

interface Tugas {
    id: number
    judul: string
    deskripsi?: string
    deadline?: string
    nilai_maksimal: number
    materi?: {
        judul: string
    }
    pelatihan?: {
        nama: string
    }
    kelas?: {
        nama: string
    }
    pengumpulan?: {
        id: number
        nilai?: number
        dikumpulkan_at?: string
    } | null
}

defineProps<{
    tugas: Tugas[]
}>()

const formatDeadline = (date?: string) => {
    if (!date) return 'Tidak ada deadline'

    return new Date(date).toLocaleString(
        'id-ID',
        {
            dateStyle: 'medium',
            timeStyle: 'short',
        }
    )
}
</script>

<template>
    <Head title="Tugas Saya" />

    <div class="min-h-screen bg-slate-50 p-6 dark:bg-slate-950">
        <div class="mx-auto max-w-6xl">

            <div class="mb-6">
                <h1
                    class="text-2xl font-bold text-slate-900 dark:text-white"
                >
                    Tugas Saya
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Lihat dan kumpulkan tugas pelatihan Anda.
                </p>
            </div>

            <div
                v-if="tugas.length === 0"
                class="rounded-xl border border-dashed
                       border-slate-300 bg-white p-12 text-center
                       dark:border-slate-700 dark:bg-slate-900"
            >
                <ClipboardList
                    class="mx-auto h-12 w-12 text-slate-400"
                />

                <h3
                    class="mt-4 font-semibold
                           text-slate-900 dark:text-white"
                >
                    Belum ada tugas
                </h3>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Belum ada tugas yang dipublikasikan.
                </p>
            </div>

            <div
                v-else
                class="grid gap-5 md:grid-cols-2"
            >
                <div
                    v-for="item in tugas"
                    :key="item.id"
                    class="rounded-xl border border-slate-200
                           bg-white p-5 shadow-sm
                           dark:border-slate-800
                           dark:bg-slate-900"
                >
                    <div class="flex items-start justify-between gap-4">

                        <div>
                            <span
                                class="text-xs font-medium
                                       text-blue-600 dark:text-blue-400"
                            >
                                {{ item.materi?.judul }}
                            </span>

                            <h2
                                class="mt-1 text-lg font-bold
                                       text-slate-900
                                       dark:text-white"
                            >
                                {{ item.judul }}
                            </h2>
                        </div>

                        <CheckCircle2
                            v-if="item.pengumpulan"
                            class="h-6 w-6 shrink-0 text-green-500"
                        />

                        <AlertCircle
                            v-else
                            class="h-6 w-6 shrink-0 text-amber-500"
                        />
                    </div>

                    <p
                        v-if="item.deskripsi"
                        class="mt-3 line-clamp-2 text-sm
                               text-slate-600
                               dark:text-slate-400"
                    >
                        {{ item.deskripsi }}
                    </p>

                    <div
                        class="mt-4 flex items-center gap-2
                               text-sm text-slate-500"
                    >
                        <Clock class="h-4 w-4" />

                        {{ formatDeadline(item.deadline) }}
                    </div>

                    <div
                        class="mt-5 flex items-center
                               justify-between"
                    >
                        <span
                            v-if="item.pengumpulan"
                            class="rounded-full bg-green-100
                                   px-3 py-1 text-xs font-medium
                                   text-green-700
                                   dark:bg-green-900/30
                                   dark:text-green-400"
                        >
                            Sudah dikumpulkan
                        </span>

                        <span
                            v-else
                            class="rounded-full bg-amber-100
                                   px-3 py-1 text-xs font-medium
                                   text-amber-700
                                   dark:bg-amber-900/30
                                   dark:text-amber-400"
                        >
                            Belum dikumpulkan
                        </span>

                        <Link
                            :href="
                                route(
                                    'peserta.tugas.show',
                                    item.id
                                )
                            "
                            class="inline-flex items-center
                                   gap-2 rounded-lg
                                   bg-blue-600 px-4 py-2
                                   text-sm font-medium
                                   text-white hover:bg-blue-700"
                        >
                            Lihat Tugas

                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>