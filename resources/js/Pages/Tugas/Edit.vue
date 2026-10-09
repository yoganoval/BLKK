<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowLeft,
    ClipboardList,
    Save,
} from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Relasi {
    id: number
    nama?: string
    judul?: string
}

interface Materi {
    id: number
    judul: string
    pelatihan?: Relasi
    kelas?: Relasi
    tahap?: Relasi
}

interface Tugas {
    id: number
    materi_id: number
    judul: string
    deskripsi?: string | null
    instruksi?: string | null
    deadline?: string | null
    nilai_maksimal: number
    status: 'draft' | 'published' | 'closed'
    materi?: Materi
    pelatihan?: Relasi
    kelas?: Relasi
    tahap?: Relasi
}

const props = defineProps<{
    tugas: Tugas
    materis: Materi[]
}>()

const formatDateTimeLocal = (value?: string | null) => {
    if (!value) return ''

    const date = new Date(value)

    if (isNaN(date.getTime())) {
        return ''
    }

    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    const hours = String(date.getHours()).padStart(2, '0')
    const minutes = String(date.getMinutes()).padStart(2, '0')

    return `${year}-${month}-${day}T${hours}:${minutes}`
}

const form = useForm({
    materi_id: props.tugas.materi_id,
    judul: props.tugas.judul ?? '',
    deskripsi: props.tugas.deskripsi ?? '',
    instruksi: props.tugas.instruksi ?? '',
    deadline: formatDateTimeLocal(props.tugas.deadline),
    nilai_maksimal: props.tugas.nilai_maksimal ?? 100,
    status: props.tugas.status ?? 'draft',
})

const selectedMateri = () => {
    return props.materis.find(
        (materi) => materi.id === Number(form.materi_id)
    )
}

const submit = () => {
    form.put(
        route('tugas.update', {
            tuga: props.tugas.id,
        })
    )
}
</script>

<template>
    <Head title="Edit Tugas" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-6">
                <Link
                    :href="route('tugas.index')"
                    class="mb-4 inline-flex items-center gap-2 text-sm text-slate-500 hover:text-indigo-600"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>

                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400"
                    >
                        <ClipboardList class="h-6 w-6" />
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                            Edit Tugas
                        </h1>

                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Perbarui informasi tugas
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form
                @submit.prevent="submit"
                class="space-y-6"
            >

                <!-- Materi -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2 class="mb-4 font-semibold text-slate-900 dark:text-white">
                        Materi
                    </h2>

                    <label class="mb-2 block text-sm font-medium">
                        Materi
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        v-model="form.materi_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                        <option :value="''">
                            Pilih Materi
                        </option>

                        <option
                            v-for="materi in materis"
                            :key="materi.id"
                            :value="materi.id"
                        >
                            {{ materi.judul }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.materi_id"
                        class="mt-1 text-sm text-red-500"
                    >
                        {{ form.errors.materi_id }}
                    </p>

                    <div
                        v-if="selectedMateri()"
                        class="mt-4 grid gap-3 sm:grid-cols-3"
                    >
                        <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
                            <div class="text-xs text-slate-400">
                                Pelatihan
                            </div>

                            <div class="mt-1 font-medium">
                                {{ selectedMateri()?.pelatihan?.nama ?? '-' }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
                            <div class="text-xs text-slate-400">
                                Kelas
                            </div>

                            <div class="mt-1 font-medium">
                                {{ selectedMateri()?.kelas?.nama ?? '-' }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
                            <div class="text-xs text-slate-400">
                                Tahap
                            </div>

                            <div class="mt-1 font-medium">
                                {{ selectedMateri()?.tahap?.nama ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2 class="mb-4 font-semibold text-slate-900 dark:text-white">
                        Detail Tugas
                    </h2>

                    <div class="space-y-5">

                        <!-- Judul -->
                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Judul Tugas
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.judul"
                                type="text"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.judul"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ form.errors.judul }}
                            </p>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Deskripsi
                            </label>

                            <textarea
                                v-model="form.deskripsi"
                                rows="4"
                                class="w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>

                            <p
                                v-if="form.errors.deskripsi"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ form.errors.deskripsi }}
                            </p>
                        </div>

                        <!-- Instruksi -->
                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Instruksi Pengerjaan
                            </label>

                            <textarea
                                v-model="form.instruksi"
                                rows="6"
                                class="w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>

                            <p
                                v-if="form.errors.instruksi"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ form.errors.instruksi }}
                            </p>
                        </div>

                        <!-- Deadline, Nilai, Status -->
                        <div class="grid gap-5 md:grid-cols-3">

                            <!-- Deadline -->
                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    Deadline
                                </label>

                                <input
                                    v-model="form.deadline"
                                    type="datetime-local"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />

                                <p
                                    v-if="form.errors.deadline"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.deadline }}
                                </p>
                            </div>

                            <!-- Nilai Maksimal -->
                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    Nilai Maksimal
                                </label>

                                <input
                                    v-model="form.nilai_maksimal"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />

                                <p
                                    v-if="form.errors.nilai_maksimal"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.nilai_maksimal }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    Status
                                </label>

                                <select
                                    v-model="form.status"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                >
                                    <option value="draft">
                                        Draft
                                    </option>

                                    <option value="published">
                                        Published
                                    </option>

                                    <option value="closed">
                                        Closed
                                    </option>
                                </select>

                                <p
                                    v-if="form.errors.status"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ form.errors.status }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Button -->
                <div class="flex justify-end gap-3">
                    <Link
                        :href="route('tugas.index')"
                        class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                    >
                        <Save class="h-4 w-4" />

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
