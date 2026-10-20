<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ArrowLeft, UploadCloud, FileText, X, Send } from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    tugas: { type: Object, default: null },
    tugasList: { type: Array, default: () => [] },
    pesertas: { type: Array, default: () => [] },
})

const form = useForm({
    tugas_id: props.tugas ? String(props.tugas.id) : '',
    peserta_id: '',
    jawaban: '',
    file: null,
})

const fileInput = ref(null)
const fileName = computed(() => form.file?.name ?? '')
const fileSize = computed(() => {
    if (!form.file) return ''
    return `${(form.file.size / 1024 / 1024).toFixed(2)} MB`
})

function selectFile(event) {
    const file = event.target.files?.[0] ?? null
    form.file = file
}

function removeFile() {
    form.file = null
    if (fileInput.value) fileInput.value.value = ''
}

function submit() {
    form.post(route('pengumpulan-tugas.store'), {
        forceFormData: true,
    })
}

onBeforeUnmount(() => {
    // Tidak membuat object URL sehingga tidak ada URL yang perlu dibersihkan.
})
</script>

<template>
    <Head title="Tambah Pengumpulan Tugas" />

    <div class="mx-auto max-w-4xl space-y-6 p-4 sm:p-6 lg:p-8">
        <Link
            :href="route('pengumpulan-tugas.index')"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600"
        >
            <ArrowLeft class="size-4" />
            Kembali ke pengumpulan tugas
        </Link>

        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                Tambah Pengumpulan Tugas
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Pilih tugas dan peserta, kemudian isi jawaban atau lampirkan berkas.
            </p>
        </div>

        <form
            class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7 dark:border-slate-800 dark:bg-slate-900"
            @submit.prevent="submit"
        >
            <div>
                <label for="tugas_id" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Tugas <span class="text-red-500">*</span>
                </label>
                <select
                    id="tugas_id"
                    v-model="form.tugas_id"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    :class="{ 'border-red-500': form.errors.tugas_id }"
                >
                    <option value="" disabled>Pilih tugas</option>
                    <option v-for="item in tugasList" :key="item.id" :value="String(item.id)">
                        {{ item.judul }}
                    </option>
                </select>
                <p v-if="form.errors.tugas_id" class="mt-1 text-sm text-red-500">
                    {{ form.errors.tugas_id }}
                </p>
            </div>

            <div>
                <label for="peserta_id" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Peserta <span class="text-red-500">*</span>
                </label>
                <select
                    id="peserta_id"
                    v-model="form.peserta_id"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    :class="{ 'border-red-500': form.errors.peserta_id }"
                >
                    <option value="" disabled>Pilih peserta</option>
                    <option v-for="item in pesertas" :key="item.id" :value="String(item.id)">
                        {{ item.nama }}
                    </option>
                </select>
                <p v-if="form.errors.peserta_id" class="mt-1 text-sm text-red-500">
                    {{ form.errors.peserta_id }}
                </p>
            </div>

            <div>
                <label for="jawaban" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Jawaban
                </label>
                <textarea
                    id="jawaban"
                    v-model="form.jawaban"
                    rows="6"
                    placeholder="Tuliskan jawaban peserta..."
                    class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    :class="{ 'border-red-500': form.errors.jawaban }"
                />
                <p v-if="form.errors.jawaban" class="mt-1 text-sm text-red-500">
                    {{ form.errors.jawaban }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Lampiran
                </label>

                <label
                    for="file"
                    class="flex cursor-pointer flex-col items-center rounded-2xl border-2 border-dashed border-slate-300 px-5 py-8 text-center transition hover:border-blue-500 hover:bg-blue-50/50 dark:border-slate-700 dark:hover:bg-blue-950/20"
                >
                    <UploadCloud class="size-9 text-blue-600" />
                    <span class="mt-3 text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Klik untuk memilih file
                    </span>
                    <span class="mt-1 text-xs text-slate-500">
                        PDF, Office, gambar, ZIP, RAR, atau video. Maksimal 50 MB.
                    </span>
                    <input
                        id="file"
                        ref="fileInput"
                        type="file"
                        class="hidden"
                        accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.webp,.zip,.rar,.mp4,.webm"
                        @change="selectFile"
                    />
                </label>

                <div
                    v-if="fileName"
                    class="mt-3 flex items-center gap-3 rounded-xl border border-slate-200 p-3 dark:border-slate-700"
                >
                    <FileText class="size-7 shrink-0 text-blue-600" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-slate-800 dark:text-white">
                            {{ fileName }}
                        </p>
                        <p class="text-xs text-slate-500">{{ fileSize }}</p>
                    </div>
                    <button type="button" class="rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-950" @click="removeFile">
                        <X class="size-4" />
                    </button>
                </div>

                <p v-if="form.errors.file" class="mt-1 text-sm text-red-500">
                    {{ form.errors.file }}
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
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <Send class="size-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Pengumpulan' }}
                </button>
            </div>
        </form>
    </div>
</template>
