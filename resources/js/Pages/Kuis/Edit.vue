
<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface ProgramPelatihan {
    id: number
    nama?: string
    judul?: string
}

interface Kelas {
    id: number
    nama: string
}

interface Materi {
    id: number
    judul: string
    program_pelatihan_id?: number
    kelas_id?: number
}

interface Kuis {
    id: number
    program_pelatihan_id: number
    kelas_id: number
    materi_id: number | null
    judul: string
    deskripsi: string | null
    waktu_menit: number | null
    nilai_lulus: number
    percobaan_maksimal: number
    status: 'draft' | 'published' | 'closed'
}

interface ProgramPelatihan {
    id: number
    nama?: string
    judul?: string
}

interface Kelas {
    id: number
    nama: string
}

interface Materi {
    id: number
    judul: string
}

const props = defineProps<{
    kuis: {
        id: number
        program_pelatihan_id: number | null
        kelas_id: number | null
        materi_id: number | null
        judul: string
        deskripsi: string | null
        waktu_menit: number | null
        nilai_lulus: number
        percobaan_maksimal: number
        status: 'draft' | 'published' | 'closed'
    }
    programPelatihan: {
        id: number
        nama: string
    }[]
    kelas: {
        id: number
        nama: string
    }[]
    materi: {
        id: number
        program_pelatihan_id: number
        kelas_id: number
        judul: string
    }[]
}>()

const form = useForm({
    program_pelatihan_id: props.kuis.program_pelatihan_id,
    kelas_id: props.kuis.kelas_id,
    materi_id: props.kuis.materi_id,
    judul: props.kuis.judul ?? '',
    deskripsi: props.kuis.deskripsi ?? '',
    waktu_menit: props.kuis.waktu_menit,
    nilai_lulus: props.kuis.nilai_lulus ?? 70,
    percobaan_maksimal: props.kuis.percobaan_maksimal ?? 1,
    status: props.kuis.status,
})

// Letakkan console.log setelah form dibuat.
console.log('DATA KUIS:', JSON.stringify(props.kuis, null, 2))
console.log('FORM:', JSON.stringify(form.data(), null, 2))

const filteredMateri = computed(() => {
    return props.materi.filter((item) => {
        const cocokProgram =
            item.program_pelatihan_id == null ||
            item.program_pelatihan_id === form.program_pelatihan_id

        const cocokKelas =
            item.kelas_id == null ||
            item.kelas_id === form.kelas_id

        return cocokProgram && cocokKelas
    })
})

watch(
    () => form.program_pelatihan_id,
    (baru, lama) => {
        if (baru !== lama) {
            form.materi_id = null
        }
    },
)

watch(
    () => form.kelas_id,
    (baru, lama) => {
        if (baru !== lama) {
            form.materi_id = null
        }
    },
)

function submit() {
    form.put(`/kuis/${props.kuis.id}`, {
        preserveScroll: true,
    })
}

function programName(program: ProgramPelatihan) {
    return program.nama ?? program.judul ?? `Program #${program.id}`
}
</script>

<template>
    <Head :title="`Edit Kuis - ${kuis.judul}`" />

    <div class="space-y-6 p-4 sm:p-6">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <div class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Akademik</span>
                    <span>/</span>
                    <Link
                        href="/kuis"
                        class="transition hover:text-blue-600"
                    >
                        Manajemen Kuis
                    </Link>
                    <span>/</span>
                    <span class="text-blue-600 dark:text-blue-400">
                        Edit Kuis
                    </span>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    Edit Kuis
                </h1>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Perbarui informasi dan pengaturan kuis yang sudah dibuat.
                </p>
            </div>

            <Link
                href="/kuis"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <span aria-hidden="true">←</span>
                Kembali
            </Link>
        </div>

        <!-- Informasi kuis -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-900/40 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-xl font-bold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        Q
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Informasi Kuis
                        </h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            ID Kuis: {{ kuis.id }}
                        </p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6 p-5 sm:p-6">
                <!-- Program dan kelas -->
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label
                            for="program_pelatihan_id"
                            class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                        >
                            Program Pelatihan
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="program_pelatihan_id"
                            v-model="form.program_pelatihan_id"
                            required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900"
                            :class="{ 'border-red-500': form.errors.program_pelatihan_id }"
                        >
                            <option :value="null" disabled>
                                Pilih program pelatihan
                            </option>
                            <option
                                v-for="program in programPelatihan"
                                :key="program.id"
                                :value="program.id"
                            >
                                {{ programName(program) }}
                            </option>
                        </select>

                        <p v-if="form.errors.program_pelatihan_id" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.program_pelatihan_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="kelas_id"
                            class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                        >
                            Kelas
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="kelas_id"
                            v-model="form.kelas_id"
                            required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900"
                            :class="{ 'border-red-500': form.errors.kelas_id }"
                        >
                            <option :value="null" disabled>
                                Pilih kelas
                            </option>
                            <option
                                v-for="item in kelas"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama }}
                            </option>
                        </select>

                        <p v-if="form.errors.kelas_id" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.kelas_id }}
                        </p>
                    </div>
                </div>

                <!-- Materi -->
                <div>
                    <label
                        for="materi_id"
                        class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Materi Pembelajaran
                    </label>

                    <select
                        id="materi_id"
                        v-model="form.materi_id"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900"
                        :class="{ 'border-red-500': form.errors.materi_id }"
                    >
                        <option :value="null">
                            Tanpa materi khusus
                        </option>
                        <option
                            v-for="item in filteredMateri"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.judul }}
                        </option>
                    </select>

                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        Pilih materi yang berkaitan dengan kuis ini.
                    </p>

                    <p v-if="form.errors.materi_id" class="mt-1.5 text-xs text-red-500">
                        {{ form.errors.materi_id }}
                    </p>
                </div>

                <!-- Judul -->
                <div>
                    <label
                        for="judul"
                        class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Judul Kuis
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="judul"
                        v-model="form.judul"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="Contoh: Kuis Dasar Microsoft Office"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900"
                        :class="{ 'border-red-500': form.errors.judul }"
                    />

                    <p v-if="form.errors.judul" class="mt-1.5 text-xs text-red-500">
                        {{ form.errors.judul }}
                    </p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label
                        for="deskripsi"
                        class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Deskripsi
                    </label>

                    <textarea
                        id="deskripsi"
                        v-model="form.deskripsi"
                        rows="4"
                        placeholder="Jelaskan tujuan dan petunjuk pengerjaan kuis..."
                        class="w-full resize-y rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900"
                        :class="{ 'border-red-500': form.errors.deskripsi }"
                    />

                    <p v-if="form.errors.deskripsi" class="mt-1.5 text-xs text-red-500">
                        {{ form.errors.deskripsi }}
                    </p>
                </div>

                <!-- Pengaturan kuis -->
                <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                    <div class="mb-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            Pengaturan Pengerjaan
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Atur durasi, standar kelulusan, dan batas percobaan peserta.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Durasi -->
                        <div>
                            <label
                                for="waktu_menit"
                                class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                            >
                                Durasi (menit)
                            </label>

                            <input
                                id="waktu_menit"
                                v-model.number="form.waktu_menit"
                                type="number"
                                min="1"
                                placeholder="Kosongkan jika bebas"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900"
                                :class="{ 'border-red-500': form.errors.waktu_menit }"
                            />

                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                Kosongkan untuk durasi tidak terbatas.
                            </p>

                            <p v-if="form.errors.waktu_menit" class="mt-1.5 text-xs text-red-500">
                                {{ form.errors.waktu_menit }}
                            </p>
                        </div>

                        <!-- Nilai lulus -->
                        <div>
                            <label
                                for="nilai_lulus"
                                class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                            >
                                Nilai Minimal Lulus
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <input
                                    id="nilai_lulus"
                                    v-model.number="form.nilai_lulus"
                                    type="number"
                                    min="0"
                                    max="100"
                                    required
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 pr-14 text-sm text-gray-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900"
                                    :class="{ 'border-red-500': form.errors.nilai_lulus }"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-400">
                                    / 100
                                </span>
                            </div>

                            <p v-if="form.errors.nilai_lulus" class="mt-1.5 text-xs text-red-500">
                                {{ form.errors.nilai_lulus }}
                            </p>
                        </div>

                        <!-- Percobaan -->
                        <div>
                            <label
                                for="percobaan_maksimal"
                                class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                            >
                                Maksimal Percobaan
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <input
                                    id="percobaan_maksimal"
                                    v-model.number="form.percobaan_maksimal"
                                    type="number"
                                    min="1"
                                    required
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 pr-20 text-sm text-gray-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900"
                                    :class="{ 'border-red-500': form.errors.percobaan_maksimal }"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-400">
                                    kali
                                </span>
                            </div>

                            <p v-if="form.errors.percobaan_maksimal" class="mt-1.5 text-xs text-red-500">
                                {{ form.errors.percobaan_maksimal }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                    <label
                        for="status"
                        class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Status Kuis
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="status"
                        v-model="form.status"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900 sm:max-w-md"
                        :class="{ 'border-red-500': form.errors.status }"
                    >
                        <option value="draft">Draft</option>
                        <option value="published">Dipublikasikan</option>
                        <option value="closed">Ditutup</option>
                    </select>

                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        Pastikan soal sudah siap sebelum kuis dipublikasikan.
                    </p>

                    <p v-if="form.errors.status" class="mt-1.5 text-xs text-red-500">
                        {{ form.errors.status }}
                    </p>
                </div>

                <!-- Error umum -->
                <div
                    v-if="form.hasErrors"
                    class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                >
                    Periksa kembali kolom yang ditandai. Perubahan belum berhasil disimpan.
                </div>

                <!-- Tombol -->
                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 dark:border-gray-700 sm:flex-row sm:justify-end">
                    <Link
                        href="/kuis"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            />
                        </svg>

                        <span>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
