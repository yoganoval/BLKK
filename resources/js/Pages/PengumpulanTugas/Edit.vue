<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineOptions({
    layout: AdminLayout,
})

interface Peserta {
    id: number
    nama: string
}

interface Tugas {
    id: number
    judul: string
    deadline?: string | null
}

interface PengumpulanTugas {
    id: number
    peserta?: Peserta | null
    tugas?: Tugas | null
    jawaban: string | null
    file_path: string | null
    dikumpulkan_at: string | null
    nilai: number | string | null
    feedback: string | null
    status: string
}

const props = defineProps({
    pengumpulanTugas: {
        type: Object,
        required: true,
    },
});


const form = useForm({
    nilai: props.pengumpulanTugas.nilai ?? '',
    feedback: props.pengumpulanTugas.feedback ?? '',
    status: props.pengumpulanTugas.status ?? 'dikumpulkan',
});

const statusOptions = [
    { value: 'belum_dikumpulkan', label: 'Belum Dikumpulkan' },
    { value: 'dikumpulkan', label: 'Dikumpulkan' },
    { value: 'dinilai', label: 'Dinilai' },
    { value: 'terlambat', label: 'Terlambat' },
]

const submit = () => {
    form.put(
        route('pengumpulan-tugas.update', {
            pengumpulan_tugas: props.pengumpulanTugas.id,
        })
    );
};

function formatTanggal(value: string | null) {
    if (!value) return '-'

    return new Date(value).toLocaleString('id-ID', {
        dateStyle: 'long',
        timeStyle: 'short',
    })
}
</script>

<template>
    <Head title="Penilaian Pengumpulan Tugas" />

    <div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                Penilaian Pengumpulan Tugas
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Periksa jawaban peserta dan berikan penilaian.
            </p>
        </div>

        <!-- Informasi pengumpulan -->
        <section
            class="space-y-5 rounded-2xl border border-slate-200
                   bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Peserta
                    </p>

                    <p class="mt-1 font-medium text-slate-900 dark:text-white">
                        {{ props.pengumpulanTugas.peserta?.nama ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-500">
                        Tugas
                    </p>

                    <p class="mt-1 font-medium text-slate-900 dark:text-white">
                        {{ props.pengumpulanTugas.tugas?.judul ?? '-' }}
                    </p>
                </div>
            </div>

            <div>
                <p class="text-sm font-semibold text-slate-500">
                    Waktu Pengumpulan
                </p>

                <p class="mt-1 text-slate-900 dark:text-white">
                    {{ formatTanggal(props.pengumpulanTugas.dikumpulkan_at) }}
                </p>
            </div>

            <div>
                <p class="text-sm font-semibold text-slate-500">
                    File Jawaban
                </p>

                <a
                    v-if="props.pengumpulanTugas.file_path"
                    :href="`/storage/${props.pengumpulanTugas.file_path}`"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-2 inline-flex items-center gap-2
                           font-medium text-indigo-600 hover:underline
                           dark:text-indigo-400"
                >
                    Lihat / Unduh File Jawaban
                </a>

                <p
                    v-else
                    class="mt-1 text-sm text-slate-500"
                >
                    Belum ada file yang dilampirkan.
                </p>
            </div>

            <div>
                <p class="text-sm font-semibold text-slate-500">
                    Jawaban Teks
                </p>

                <p
                    class="mt-2 whitespace-pre-wrap text-slate-800
                           dark:text-slate-200"
                >
                    {{ props.pengumpulanTugas.jawaban || 'Belum ada jawaban teks.' }}
                </p>
            </div>
        </section>

        <!-- Form penilaian -->
        <form
            @submit.prevent="submit"
            class="space-y-6 rounded-2xl border border-slate-200
                   bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
        >
            <div>
                <label
                    for="nilai"
                    class="mb-2 block text-sm font-semibold
                           text-slate-700 dark:text-slate-200"
                >
                    Nilai (0–100)
                </label>

                <input
                    id="nilai"
                    v-model="form.nilai"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="Masukkan nilai"
                    class="w-full rounded-xl border border-slate-300
                           bg-white px-4 py-3 text-slate-900
                           outline-none focus:border-indigo-500
                           focus:ring-2 focus:ring-indigo-500/20
                           dark:border-slate-700 dark:bg-slate-950
                           dark:text-white"
                />

                <p
                    v-if="form.errors.nilai"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ form.errors.nilai }}
                </p>
            </div>

            <div>
                <label
                    for="feedback"
                    class="mb-2 block text-sm font-semibold
                           text-slate-700 dark:text-slate-200"
                >
                    Umpan Balik
                </label>

                <textarea
                    id="feedback"
                    v-model="form.feedback"
                    rows="5"
                    placeholder="Berikan komentar atau masukan kepada peserta..."
                    class="w-full rounded-xl border border-slate-300
                           bg-white px-4 py-3 text-slate-900
                           outline-none focus:border-indigo-500
                           focus:ring-2 focus:ring-indigo-500/20
                           dark:border-slate-700 dark:bg-slate-950
                           dark:text-white"
                />

                <p
                    v-if="form.errors.feedback"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ form.errors.feedback }}
                </p>
            </div>

            <div>
                <label
                    for="file"
                    class="mb-2 block text-sm font-semibold
                           text-slate-700 dark:text-slate-200"
                >
                    Ganti File Jawaban (Opsional)
                </label>

                <input
                    id="file"
                    type="file"
                    accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.webp,.zip,.rar,.mp4,.webm"
                    @change="form.file = $event.target.files?.[0] ?? null"
                    class="block w-full rounded-xl border border-slate-300
                           bg-white p-3 text-sm text-slate-700
                           dark:border-slate-700 dark:bg-slate-950
                           dark:text-slate-200"
                />

                <p class="mt-1 text-xs text-slate-500">
                    Maksimal 50 MB. Kosongkan jika tidak ingin mengganti file.
                </p>

                <p
                    v-if="form.errors.file"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ form.errors.file }}
                </p>
            </div>

            <div>
                <label
                    for="status"
                    class="mb-2 block text-sm font-semibold
                           text-slate-700 dark:text-slate-200"
                >
                    Status
                </label>

                <select
                    id="status"
                    v-model="form.status"
                    class="w-full rounded-xl border border-slate-300
                           bg-white px-4 py-3 text-slate-900
                           outline-none focus:border-indigo-500
                           dark:border-slate-700 dark:bg-slate-950
                           dark:text-white"
                >
                    <option
                        v-for="option in statusOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>

                <p
                    v-if="form.errors.status"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ form.errors.status }}
                </p>
            </div>

            <div
                class="flex flex-wrap justify-end gap-3 border-t
                       border-slate-200 pt-5 dark:border-slate-800"
            >
                <Link
                    :href="route('pengumpulan-tugas.index')"
                    class="inline-flex items-center justify-center
                           rounded-xl border border-slate-300 px-5 py-3
                           text-sm font-semibold text-slate-700
                           hover:bg-slate-100 dark:border-slate-700
                           dark:text-slate-200 dark:hover:bg-slate-800"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center
                           rounded-xl bg-indigo-600 px-5 py-3
                           text-sm font-semibold text-white
                           hover:bg-indigo-700 disabled:cursor-not-allowed
                           disabled:opacity-50"
                >
                    {{
                        form.processing
                            ? 'Menyimpan...'
                            : 'Simpan Penilaian'
                    }}
                </button>
            </div>
        </form>
    </div>
</template>
