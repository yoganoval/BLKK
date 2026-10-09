<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowLeft,
    Upload,
    FileText,
    Clock,
    CheckCircle2,
} from 'lucide-vue-next'

interface Tugas {
    id: number
    judul: string
    deskripsi?: string
    instruksi?: string
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

    tahap?: {
        nama: string
    }
}

interface Pengumpulan {
    id: number
    file: string
    catatan?: string
    nilai?: number
    feedback?: string
    dikumpulkan_at?: string
}

const props = defineProps<{
    tugas: Tugas
    pengumpulan?: Pengumpulan | null
}>()

const form = useForm<{
    file: File | null
    catatan: string
}>({
    file: null,
    catatan: props.pengumpulan?.catatan ?? '',
})

const submit = () => {
    form.post(
        route(
            'peserta.tugas.kumpulkan',
            props.tugas.id
        ),
        {
            forceFormData: true,
            preserveScroll: true,
        }
    )
}

const formatDate = (date?: string) => {
    if (!date) return '-'

    return new Date(date).toLocaleString(
        'id-ID',
        {
            dateStyle: 'long',
            timeStyle: 'short',
        }
    )
}
</script>

<template>
    <Head :title="tugas.judul" />

    <div class="min-h-screen bg-slate-50 p-6 dark:bg-slate-950">
        <div class="mx-auto max-w-4xl">

            <Link
                :href="route('peserta.tugas.index')"
                class="mb-5 inline-flex items-center
                       gap-2 text-sm text-slate-600
                       hover:text-blue-600
                       dark:text-slate-400"
            >
                <ArrowLeft class="h-4 w-4" />
                Kembali ke tugas
            </Link>

            <!-- Detail tugas -->
            <div
                class="rounded-xl border border-slate-200
                       bg-white p-6 shadow-sm
                       dark:border-slate-800
                       dark:bg-slate-900"
            >
                <div
                    class="text-sm text-blue-600
                           dark:text-blue-400"
                >
                    {{ tugas.materi?.judul }}
                </div>

                <h1
                    class="mt-1 text-2xl font-bold
                           text-slate-900
                           dark:text-white"
                >
                    {{ tugas.judul }}
                </h1>

                <div
                    class="mt-4 grid gap-3 text-sm
                           text-slate-500 sm:grid-cols-3"
                >
                    <div>
                        <span class="font-medium">
                            Pelatihan
                        </span>
                        <br />
                        {{ tugas.pelatihan?.nama ?? '-' }}
                    </div>

                    <div>
                        <span class="font-medium">
                            Kelas
                        </span>
                        <br />
                        {{ tugas.kelas?.nama ?? '-' }}
                    </div>

                    <div>
                        <span class="font-medium">
                            Deadline
                        </span>
                        <br />
                        {{ formatDate(tugas.deadline) }}
                    </div>
                </div>

                <div
                    v-if="tugas.deskripsi"
                    class="mt-6"
                >
                    <h2
                        class="font-semibold
                               text-slate-900
                               dark:text-white"
                    >
                        Deskripsi
                    </h2>

                    <p
                        class="mt-2 whitespace-pre-line
                               text-sm leading-6
                               text-slate-600
                               dark:text-slate-400"
                    >
                        {{ tugas.deskripsi }}
                    </p>
                </div>

                <div
                    v-if="tugas.instruksi"
                    class="mt-6"
                >
                    <h2
                        class="font-semibold
                               text-slate-900
                               dark:text-white"
                    >
                        Instruksi
                    </h2>

                    <p
                        class="mt-2 whitespace-pre-line
                               text-sm leading-6
                               text-slate-600
                               dark:text-slate-400"
                    >
                        {{ tugas.instruksi }}
                    </p>
                </div>
            </div>

            <!-- Sudah dikumpulkan -->
            <div
                v-if="pengumpulan"
                class="mt-5 rounded-xl border
                       border-green-200
                       bg-green-50 p-5
                       dark:border-green-900
                       dark:bg-green-950/30"
            >
                <div class="flex items-center gap-3">
                    <CheckCircle2
                        class="h-6 w-6 text-green-600"
                    />

                    <div>
                        <h2
                            class="font-semibold
                                   text-green-800
                                   dark:text-green-300"
                        >
                            Tugas sudah dikumpulkan
                        </h2>

                        <p
                            class="text-sm text-green-700
                                   dark:text-green-400"
                        >
                            {{ formatDate(
                                pengumpulan.dikumpulkan_at
                            ) }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="pengumpulan.nilai !== null &&
                           pengumpulan.nilai !== undefined"
                    class="mt-4"
                >
                    <span
                        class="text-sm text-slate-600
                               dark:text-slate-400"
                    >
                        Nilai
                    </span>

                    <div
                        class="text-3xl font-bold
                               text-green-700
                               dark:text-green-400"
                    >
                        {{ pengumpulan.nilai }}
                    </div>
                </div>

                <div
                    v-if="pengumpulan.feedback"
                    class="mt-4"
                >
                    <span
                        class="text-sm font-medium"
                    >
                        Feedback instruktur
                    </span>

                    <p
                        class="mt-1 whitespace-pre-line
                               text-sm text-slate-600
                               dark:text-slate-400"
                    >
                        {{ pengumpulan.feedback }}
                    </p>
                </div>
            </div>

            <!-- Form upload -->
            <div
                class="mt-5 rounded-xl border
                    border-slate-200 bg-white p-6
                    shadow-sm
                    dark:border-slate-800
                    dark:bg-slate-900"
            >
                <h2
                    class="text-lg font-bold
                        text-slate-900
                        dark:text-white"
                >
                    {{
                        pengumpulan
                            ? 'Ganti Pengumpulan'
                            : 'Kumpulkan Tugas'
                    }}
                </h2>

                <p
                    class="mt-1 text-sm
                        text-slate-500"
                >
                    Upload file jawaban tugas Anda.
                </p>

                <form
                    class="mt-5 space-y-5"
                    @submit.prevent="submit"
                >
                    <!-- File -->
                    <div>
                        <label
                            class="mb-2 block text-sm
                                font-medium"
                        >
                            File Jawaban
                        </label>

                        <label
                            class="flex cursor-pointer
                                flex-col items-center
                                justify-center rounded-xl
                                border-2 border-dashed
                                border-slate-300 p-8
                                hover:border-blue-500
                                dark:border-slate-700"
                        >
                            <Upload
                                class="mb-3 h-8 w-8
                                    text-slate-400"
                            />

                            <span
                                class="text-sm
                                    text-slate-600
                                    dark:text-slate-400"
                            >
                                {{
                                    form.file
                                        ? form.file.name
                                        : 'Klik untuk memilih file'
                                }}
                            </span>

                            <span
                                class="mt-1 text-xs
                                    text-slate-400"
                            >
                                PDF, Word, Excel, PowerPoint,
                                ZIP/RAR. Maks. 10 MB.
                            </span>

                            <input
                                type="file"
                                class="hidden"
                                @change="handleFileChange"
                            />
                        </label>

                        <p
                            v-if="form.errors.file"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.file }}
                        </p>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label
                            class="mb-2 block text-sm
                                font-medium"
                        >
                            Catatan
                        </label>

                        <textarea
                            v-model="form.catatan"
                            rows="4"
                            class="w-full rounded-lg
                                border border-slate-300
                                dark:border-slate-700
                                dark:bg-slate-800"
                            placeholder="Tambahkan catatan jika diperlukan..."
                        ></textarea>

                        <p
                            v-if="form.errors.catatan"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.catatan }}
                        </p>
                    </div>

                    <!-- Button -->
                    <button
                        type="submit"
                        :disabled="
                            form.processing ||
                            !form.file
                        "
                        class="inline-flex w-full
                            items-center
                            justify-center gap-2
                            rounded-lg bg-blue-600
                            px-5 py-3
                            font-medium text-white
                            hover:bg-blue-700
                            disabled:cursor-not-allowed
                            disabled:opacity-50"
                    >
                        <FileText class="h-5 w-5" />

                        {{
                            form.processing
                                ? 'Mengupload...'
                                : 'Kumpulkan Tugas'
                        }}
                    </button>
                </form>
            </div>


        </div>
    </div>
</template>