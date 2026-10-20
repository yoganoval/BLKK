
<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import PesertaLayout from '@/Layouts/PesertaLayout.vue'

defineOptions({
    layout: PesertaLayout,
})

const props = defineProps({
    tugas: {
        type: Object,
        required: true,
    },
    pengumpulan: {
        type: Object,
        default: null,
    },
})

const page = usePage()

const form = useForm({
    jawaban: props.pengumpulan?.jawaban ?? '',
    file: null,
})

const flashSuccess = computed(() => page.props.flash?.success)

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    const parsed = new Date(
        /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(tanggal)
            ? tanggal.replace(' ', 'T')
            : tanggal
    )

    if (Number.isNaN(parsed.getTime())) return tanggal

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'long',
        timeStyle: 'short',
        hour12: false,
    }).format(parsed)
}

const statusLabel = computed(() => {
    const status = props.pengumpulan?.status

    const labels = {
        dikumpulkan: 'Sudah dikumpulkan',
        terlambat: 'Terlambat',
        dinilai: 'Sudah dinilai',
        diperiksa: 'Sedang diperiksa',
    }

    return labels[status] ?? status ?? 'Belum dikumpulkan'
})

const statusClass = computed(() => {
    const classes = {
        dinilai:
            'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950/50 dark:text-emerald-400 dark:ring-emerald-500/20',
        terlambat:
            'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-950/50 dark:text-red-400 dark:ring-red-500/20',
        dikumpulkan:
            'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-950/50 dark:text-blue-400 dark:ring-blue-500/20',
        diperiksa:
            'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950/50 dark:text-amber-400 dark:ring-amber-500/20',
    }

    return classes[props.pengumpulan?.status] ??
        'bg-gray-100 text-gray-600 ring-gray-500/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600/30'
})

const pilihFile = (event) => {
    form.file = event.target.files?.[0] ?? null
}

const kumpulkanTugas = () => {
    form.post(route('peserta.tugas.store', props.tugas.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.file = null
        },
    })
}
</script>

<template>
    <Head :title="`${tugas.judul} - Detail Tugas`" />

    <div class="min-h-screen bg-gray-50 px-4 py-6 transition-colors duration-300 dark:bg-gray-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl space-y-6">

            <!-- Navigasi -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                        LMS BLKK / Tugas
                    </p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                        Detail Tugas
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Pelajari instruksi dan kumpulkan jawaban tugasmu.
                    </p>
                </div>

                <Link
                    :href="route('peserta.tugas.index')"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </Link>
            </div>

            <!-- Notifikasi berhasil -->
            <div
                v-if="flashSuccess"
                role="status"
                class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ flashSuccess }}</span>
            </div>

            <!-- Header tugas -->
            <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 p-6 text-white shadow-lg sm:p-8">
                <div class="pointer-events-none absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-20 right-1/3 h-48 w-48 rounded-full bg-indigo-300/20 blur-3xl"></div>

                <div class="relative">
                    <div class="mb-5 flex flex-wrap items-center gap-2">
                        <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold">
                            Tugas pembelajaran
                        </span>

                        <span
                            class="rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset"
                            :class="{
                                'bg-emerald-100 text-emerald-800 ring-emerald-200': pengumpulan?.status === 'dinilai',
                                'bg-red-100 text-red-800 ring-red-200': pengumpulan?.status === 'terlambat',
                                'bg-amber-100 text-amber-800 ring-amber-200': pengumpulan?.status === 'diperiksa',
                                'bg-blue-100 text-blue-800 ring-blue-200': pengumpulan?.status === 'dikumpulkan',
                                'bg-white/15 text-white ring-white/20': !pengumpulan?.status,
                            }"
                        >
                            {{ statusLabel }}
                        </span>
                    </div>

                    <h2 class="max-w-3xl break-words text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl">
                        {{ tugas.judul }}
                    </h2>

                    <p class="mt-4 text-sm leading-6 text-blue-100 sm:text-base">
                        {{
                            tugas.program_pelatihan?.pelatihan?.nama
                            ?? tugas.program_pelatihan?.pelatihan?.nama_pelatihan
                            ?? 'Program pelatihan'
                        }}
                        <span v-if="tugas.kelas?.nama">
                            · Kelas {{ tugas.kelas.nama }}
                        </span>
                    </p>

                    <div class="mt-7 grid gap-3 border-t border-white/20 pt-5 sm:grid-cols-2">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-blue-200">Batas pengumpulan</p>
                                <p class="mt-1 text-sm font-semibold">
                                    {{ formatTanggal(tugas.deadline) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-blue-200">Waktu pengumpulan</p>
                                <p class="mt-1 text-sm font-semibold">
                                    {{ formatTanggal(pengumpulan?.dikumpulkan_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Instruksi tugas -->
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-5 dark:border-gray-800 sm:px-7">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">
                            Instruksi Tugas
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Baca petunjuk sebelum mengerjakan tugas.
                        </p>
                    </div>
                </div>

                <div class="p-5 sm:p-7">
                    <div class="whitespace-pre-line break-words text-sm leading-8 text-gray-700 dark:text-gray-300 sm:text-base">
                        {{ tugas.instruksi || tugas.deskripsi || 'Tidak ada instruksi tambahan dari instruktur.' }}
                    </div>
                </div>
            </section>

            <!-- Form pengumpulan -->
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-100 px-5 py-5 dark:border-gray-800 sm:px-7">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v4h16v-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ pengumpulan ? 'Perbarui Pengumpulan' : 'Kumpulkan Tugas' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Tulis jawaban atau lampirkan file tugas.
                            </p>
                        </div>
                    </div>
                </div>

                <form class="space-y-6 p-5 sm:p-7" @submit.prevent="kumpulkanTugas">
                    <!-- Jawaban teks -->
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="jawaban" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                Jawaban Teks
                            </label>
                            <span class="text-xs text-gray-400">
                                {{ form.jawaban.length.toLocaleString('id-ID') }} / 50.000
                            </span>
                        </div>

                        <textarea
                            id="jawaban"
                            v-model="form.jawaban"
                            rows="7"
                            maxlength="50000"
                            placeholder="Tuliskan jawaban tugasmu di sini..."
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm leading-7 text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200 dark:placeholder:text-gray-600 dark:focus:border-blue-500 dark:focus:ring-blue-950/60"
                            :aria-invalid="!!form.errors.jawaban"
                            aria-describedby="jawaban-error"
                        ></textarea>

                        <p
                            id="jawaban-error"
                            v-if="form.errors.jawaban"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ form.errors.jawaban }}
                        </p>
                    </div>

                    <!-- Upload file -->
                    <div>
                        <label for="file" class="mb-2 block text-sm font-semibold text-gray-800 dark:text-gray-200">
                            Lampiran Tugas
                        </label>

                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-950/50 sm:p-5">
                            <div
                                v-if="pengumpulan?.file_path"
                                class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-blue-100 bg-blue-50 p-3 dark:border-blue-900 dark:bg-blue-950/30"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-blue-600 dark:bg-gray-900 dark:text-blue-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6M8 13h8M8 17h8" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            File yang sudah dikumpulkan
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Kamu dapat memilih file baru untuk menggantinya.
                                        </p>
                                    </div>
                                </div>

                                <a
                                    :href="`/storage/${pengumpulan.file_path}`"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sm font-semibold text-blue-700 hover:underline dark:text-blue-400"
                                >
                                    Lihat file
                                </a>
                            </div>

                            <input
                                id="file"
                                type="file"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png"
                                class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-400 dark:file:bg-blue-950/60 dark:file:text-blue-400 dark:hover:file:bg-blue-900/60"
                                @change="pilihFile"
                            />

                            <p class="mt-3 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                Format yang diterima: PDF, Word, Excel, PowerPoint, ZIP, JPG, dan PNG. Ukuran maksimal 10 MB, sesuai validasi server.
                            </p>

                            <div
                                v-if="form.file"
                                class="mt-3 flex items-center gap-2 rounded-lg bg-white p-3 text-sm text-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="min-w-0 break-all">{{ form.file.name }}</span>
                            </div>
                        </div>

                        <p v-if="form.errors.file" class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.file }}
                        </p>
                    </div>

                    <!-- Penilaian -->
                    <div
                        v-if="pengumpulan"
                        class="rounded-2xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-950/60"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15l-2 5-1.5-3.5L5 15l5-1 2-4 2 4 5 1-3.5 1.5L16 20l-4-5zM12 2v3m8 1l-2 2M4 6l2 2" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">
                                    Hasil Penilaian
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Informasi dari instruktur.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    Nilai
                                </p>
                                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ pengumpulan.nilai ?? '—' }}
                                    <span v-if="pengumpulan.nilai != null" class="text-sm font-medium text-gray-400">
                                        / {{ tugas.nilai_maksimal ?? 100 }}
                                    </span>
                                </p>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    Waktu penilaian
                                </p>
                                <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ formatTanggal(pengumpulan.dinilai_at) }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="pengumpulan.feedback"
                            class="mt-4 rounded-xl border border-blue-100 bg-blue-50/70 p-4 dark:border-blue-900 dark:bg-blue-950/30"
                        >
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-300">
                                Feedback Instruktur
                            </p>
                            <p class="mt-2 whitespace-pre-line break-words text-sm leading-7 text-gray-700 dark:text-gray-300">
                                {{ pengumpulan.feedback }}
                            </p>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 dark:border-gray-800 sm:flex-row sm:justify-end">
                        <Link
                            :href="route('peserta.tugas.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            Kembali
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-blue-900"
                        >
                            <svg
                                v-if="!form.processing"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v4h16v-4" />
                            </svg>

                            <svg
                                v-else
                                class="h-4 w-4 animate-spin"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>

                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : pengumpulan
                                        ? 'Perbarui Pengumpulan'
                                        : 'Kumpulkan Tugas'
                            }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</template>
