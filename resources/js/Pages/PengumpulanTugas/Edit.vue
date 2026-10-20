<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ArrowLeft, Save, Award } from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    pengumpulanTugas: { type: Object, required: true },
})

const item = computed(() => props.pengumpulanTugas)

const form = useForm({
    nilai: item.value.nilai ?? '',
    feedback: item.value.feedback ?? '',
    status: item.value.status ?? 'dikumpulkan',
})

const statuses = [
    { value: 'belum_dikumpulkan', label: 'Belum dikumpulkan' },
    { value: 'dikumpulkan', label: 'Dikumpulkan' },
    { value: 'dinilai', label: 'Sudah dinilai' },
    { value: 'terlambat', label: 'Terlambat' },
]

function submit() {
    form.put(route('pengumpulan-tugas.update', item.value.id))
}
</script>

<template>
    <Head title="Penilaian Tugas" />

    <div class="mx-auto max-w-4xl space-y-6 p-4 sm:p-6 lg:p-8">
        <Link
            :href="route('pengumpulan-tugas.index')"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600"
        >
            <ArrowLeft class="size-4" />
            Kembali ke pengumpulan tugas
        </Link>

        <div>
            <p class="text-sm font-medium text-blue-600">Penilaian Akademik</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                Penilaian Tugas
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Periksa pengumpulan peserta dan berikan nilai serta feedback.
            </p>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-7 dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                Informasi Pengumpulan
            </h2>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-slate-500">Nama Peserta</p>
                    <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                        {{ item.peserta?.nama ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Judul Tugas</p>
                    <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                        {{ item.tugas?.judul ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="mt-5 border-t border-slate-200 pt-5 dark:border-slate-800">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Jawaban peserta
                </p>
                <p class="mt-2 whitespace-pre-wrap break-words text-sm leading-7 text-slate-700 dark:text-slate-300">
                    {{ item.jawaban || 'Tidak ada jawaban teks.' }}
                </p>

                <a
                    v-if="item.file_path"
                    :href="`/storage/${item.file_path.split('/').map(encodeURIComponent).join('/')}`"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700"
                >
                    Buka lampiran peserta
                </a>
            </div>
        </section>

        <form
            class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7 dark:border-slate-800 dark:bg-slate-900"
            @submit.prevent="submit"
        >
            <div class="flex items-center gap-2">
                <Award class="size-5 text-blue-600" />
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    Form Penilaian
                </h2>
            </div>

            <div>
                <label for="nilai" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Nilai (0–100)
                </label>
                <input
                    id="nilai"
                    v-model="form.nilai"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="Contoh: 85"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    :class="{ 'border-red-500': form.errors.nilai }"
                />
                <p v-if="form.errors.nilai" class="mt-1 text-sm text-red-500">
                    {{ form.errors.nilai }}
                </p>
            </div>

            <div>
                <label for="feedback" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Feedback / Umpan Balik
                </label>
                <textarea
                    id="feedback"
                    v-model="form.feedback"
                    rows="5"
                    maxlength="5000"
                    placeholder="Berikan masukan, koreksi, atau saran kepada peserta..."
                    class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    :class="{ 'border-red-500': form.errors.feedback }"
                />
                <p class="mt-1 text-xs text-slate-500">
                    {{ form.feedback.length }}/5000 karakter
                </p>
                <p v-if="form.errors.feedback" class="mt-1 text-sm text-red-500">
                    {{ form.errors.feedback }}
                </p>
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Status Pengumpulan
                </label>
                <select
                    id="status"
                    v-model="form.status"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    :class="{ 'border-red-500': form.errors.status }"
                >
                    <option v-for="status in statuses" :key="status.value" :value="status.value">
                        {{ status.label }}
                    </option>
                </select>
                <p v-if="form.errors.status" class="mt-1 text-sm text-red-500">
                    {{ form.errors.status }}
                </p>
                <p v-if="form.status === 'dinilai'" class="mt-2 text-xs text-emerald-600">
                    Status ini mengharuskan nilai diisi sebelum disimpan.
                </p>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end dark:border-slate-800">
                <Link
                    :href="route('pengumpulan-tugas.index')"
                    class="rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
                >
                    <Save class="size-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Penilaian' }}
                </button>
            </div>
        </form>
    </div>
</template>
