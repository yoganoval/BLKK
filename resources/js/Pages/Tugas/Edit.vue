<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface ProgramPelatihan {
    id: number
    nama?: string
    pelatihan?: {
        id?: number
        nama: string
    } | null
    tahap?: {
        id?: number
        nama: string
    } | null
}

interface Kelas {
    id: number
    nama: string
    durasi_hari?: number
}

interface Tugas {
    id: number
    program_pelatihan_id: number
    kelas_id: number
    judul: string
    instruksi: string | null
    deadline: string | null
    nilai_maksimal: number | string
    status: 'draft' | 'published' | 'closed'
    program_pelatihan?: ProgramPelatihan | null
    kelas?: Kelas | null
    pembuat?: {
        id?: number
        name?: string
    } | null
}

const props = defineProps<{
    tugas: Tugas
    programPelatihans: ProgramPelatihan[]
    kelass: Kelas[]
}>()

/**
 * Format tanggal untuk input datetime-local.
 * Tidak melakukan konversi zona waktu agar jam yang
 * tersimpan tidak berubah secara tidak sengaja.
 */
function formatDeadline(value: string | null): string {
    if (!value) return ''

    const match = value.match(
        /^(\d{4}-\d{2}-\d{2})[T ](\d{2}:\d{2})/
    )

    return match ? `${match[1]}T${match[2]}` : ''
}

/**
 * Form diisi dari data tugas yang diterima dari Laravel.
 */
const form = useForm({
    program_pelatihan_id: String(
        props.tugas.program_pelatihan_id ?? ''
    ),
    kelas_id: String(
        props.tugas.kelas_id ?? ''
    ),
    judul: props.tugas.judul ?? '',
    instruksi: props.tugas.instruksi ?? '',
    deadline: formatDeadline(props.tugas.deadline),
    nilai_maksimal: Number(
        props.tugas.nilai_maksimal ?? 100
    ),
    status: props.tugas.status ?? 'draft',
})

/**
 * Kirim perubahan ke TugasController@update.
 * Parameter route mengikuti nama {tuga} di Laravel.
 */
function submit() {
    form.put(
        route('tugas.update', {
            tuga: props.tugas.id,
        }),
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>
    <Head :title="`Edit Tugas: ${tugas.judul}`" />

    <div class="mx-auto max-w-4xl space-y-6 p-4 sm:p-6">
        <!-- Header -->
        <div>
            <Link
                :href="route('tugas.index')"
                class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 transition hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
                <span aria-hidden="true">←</span>
                Kembali ke daftar tugas
            </Link>

            <div class="mt-4">
                <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white sm:text-3xl">
                    Edit Tugas
                </h1>

                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Perbarui informasi tugas dan simpan perubahan.
                </p>
            </div>
        </div>

        <!-- Informasi tugas -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="font-semibold text-slate-800 dark:text-white">
                        Informasi Tugas
                    </h2>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        ID tugas: #{{ tugas.id }}
                    </p>
                </div>

                <span
                    class="inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-semibold"
                    :class="{
                        'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200':
                            tugas.status === 'draft',
                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300':
                            tugas.status === 'published',
                        'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300':
                            tugas.status === 'closed',
                    }"
                >
                    {{
                        tugas.status === 'draft'
                            ? 'Draft'
                            : tugas.status === 'published'
                              ? 'Dipublikasikan'
                              : 'Ditutup'
                    }}
                </span>
            </div>

            <form
                @submit.prevent="submit"
                class="space-y-6 p-5 sm:p-6"
            >
                <!-- Program pelatihan -->
                <div>
                    <label
                        for="program_pelatihan_id"
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Program Pelatihan
                        <span class="text-rose-500">*</span>
                    </label>

                    <select
                        id="program_pelatihan_id"
                        v-model="form.program_pelatihan_id"
                        required
                        class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-900 dark:text-white"
                        :class="form.errors.program_pelatihan_id
                            ? 'border-rose-500'
                            : 'border-slate-300 dark:border-slate-600'"
                    >
                        <option value="">
                            Pilih program pelatihan
                        </option>

                        <option
                            v-for="program in programPelatihans"
                            :key="program.id"
                            :value="String(program.id)"
                        >
                            {{
                                program.pelatihan?.nama
                                ?? program.nama
                                ?? `Program #${program.id}`
                            }}
                            {{
                                program.tahap?.nama
                                    ? ` - Tahap ${program.tahap.nama}`
                                    : ''
                            }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.program_pelatihan_id"
                        class="mt-1 text-sm text-rose-500"
                    >
                        {{ form.errors.program_pelatihan_id }}
                    </p>
                </div>

                <!-- Kelas -->
                <div>
                    <label
                        for="kelas_id"
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Kelas
                        <span class="text-rose-500">*</span>
                    </label>

                    <select
                        id="kelas_id"
                        v-model="form.kelas_id"
                        required
                        class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-900 dark:text-white"
                        :class="form.errors.kelas_id
                            ? 'border-rose-500'
                            : 'border-slate-300 dark:border-slate-600'"
                    >
                        <option value="">
                            Pilih kelas
                        </option>

                        <option
                            v-for="kelas in kelass"
                            :key="kelas.id"
                            :value="String(kelas.id)"
                        >
                            {{ kelas.nama }}
                            {{
                                kelas.durasi_hari
                                    ? ` (${kelas.durasi_hari} hari)`
                                    : ''
                            }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.kelas_id"
                        class="mt-1 text-sm text-rose-500"
                    >
                        {{ form.errors.kelas_id }}
                    </p>
                </div>

                <!-- Judul -->
                <div>
                    <label
                        for="judul"
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Judul Tugas
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        id="judul"
                        v-model="form.judul"
                        type="text"
                        maxlength="255"
                        required
                        placeholder="Masukkan judul tugas"
                        class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-900 dark:text-white"
                        :class="form.errors.judul
                            ? 'border-rose-500'
                            : 'border-slate-300 dark:border-slate-600'"
                    />

                    <p
                        v-if="form.errors.judul"
                        class="mt-1 text-sm text-rose-500"
                    >
                        {{ form.errors.judul }}
                    </p>
                </div>

                <!-- Instruksi -->
                <div>
                    <label
                        for="instruksi"
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Instruksi Tugas
                    </label>

                    <textarea
                        id="instruksi"
                        v-model="form.instruksi"
                        rows="6"
                        placeholder="Tuliskan instruksi pengerjaan tugas..."
                        class="w-full resize-y rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-900 dark:text-white"
                        :class="form.errors.instruksi
                            ? 'border-rose-500'
                            : 'border-slate-300 dark:border-slate-600'"
                    />

                    <p
                        v-if="form.errors.instruksi"
                        class="mt-1 text-sm text-rose-500"
                    >
                        {{ form.errors.instruksi }}
                    </p>
                </div>

                <!-- Deadline dan nilai -->
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label
                            for="deadline"
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Deadline Pengumpulan
                        </label>

                        <input
                            id="deadline"
                            v-model="form.deadline"
                            type="datetime-local"
                            class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-900 dark:text-white"
                            :class="form.errors.deadline
                                ? 'border-rose-500'
                                : 'border-slate-300 dark:border-slate-600'"
                        />

                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Kosongkan jika tugas tidak memiliki batas waktu.
                        </p>

                        <p
                            v-if="form.errors.deadline"
                            class="mt-1 text-sm text-rose-500"
                        >
                            {{ form.errors.deadline }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="nilai_maksimal"
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Nilai Maksimal
                            <span class="text-rose-500">*</span>
                        </label>

                        <input
                            id="nilai_maksimal"
                            v-model.number="form.nilai_maksimal"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-900 dark:text-white"
                            :class="form.errors.nilai_maksimal
                                ? 'border-rose-500'
                                : 'border-slate-300 dark:border-slate-600'"
                        />

                        <p
                            v-if="form.errors.nilai_maksimal"
                            class="mt-1 text-sm text-rose-500"
                        >
                            {{ form.errors.nilai_maksimal }}
                        </p>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label
                        for="status"
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Status Tugas
                        <span class="text-rose-500">*</span>
                    </label>

                    <select
                        id="status"
                        v-model="form.status"
                        required
                        class="w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-900 dark:text-white"
                        :class="form.errors.status
                            ? 'border-rose-500'
                            : 'border-slate-300 dark:border-slate-600'"
                    >
                        <option value="draft">
                            Draft — belum dipublikasikan
                        </option>

                        <option value="published">
                            Dipublikasikan
                        </option>

                        <option value="closed">
                            Ditutup
                        </option>
                    </select>

                    <p
                        v-if="form.errors.status"
                        class="mt-1 text-sm text-rose-500"
                    >
                        {{ form.errors.status }}
                    </p>
                </div>

                <!-- Tombol aksi -->
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end dark:border-slate-700">
                    <Link
                        :href="route('tugas.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
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
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                            />
                        </svg>

                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Perubahan'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
