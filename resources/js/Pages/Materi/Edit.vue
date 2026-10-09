<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Pelatihan {
    id: number;
    nama: string;
}

interface Kelas {
    id: number;
    nama: string;
}

interface Tahap {
    id: number;
    nama: string;
}

interface MateriFile {
    id: number;
    nama_file: string;
    file_path: string;
    tipe_file: string | null;
    ukuran: number | null;
}

interface Materi {
    id: number;
    pelatihan_id: number;
    kelas_id: number;
    tahap_id: number | null;
    judul: string;
    deskripsi: string | null;
    isi_materi: string | null;
    urutan: number;
    status: 'draft' | 'published';
    files: MateriFile[];
}

interface Props {
    materi: Materi;
    pelatihans: Pelatihan[];
    kelas: Kelas[];
    tahaps: Tahap[];
}

const props = defineProps<Props>();

const form = useForm({
    pelatihan_id: props.materi.pelatihan_id,
    kelas_id: props.materi.kelas_id,
    tahap_id: props.materi.tahap_id ?? '',
    judul: props.materi.judul,
    deskripsi: props.materi.deskripsi ?? '',
    isi_materi: props.materi.isi_materi ?? '',
    urutan: props.materi.urutan,
    status: props.materi.status,
});

const fileForm = useForm<{
    file: File | null;
}>({
    file: null,
});

const fileInput = ref<HTMLInputElement | null>(null);

function submit() {
    form.put(route('materi.update', props.materi.id));
}

function uploadFile() {
    if (!fileForm.file) {
        return;
    }

    fileForm.post(route('materi.files.store', props.materi.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            fileForm.reset();
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
}

function deleteFile(id: number) {
    if (!confirm('Hapus file materi ini?')) {
        return;
    }

    router.delete(route('materi.files.destroy', id), {
        preserveScroll: true,
    });
}

function formatSize(size: number | null) {
    if (!size) return '-';

    if (size < 1024) {
        return `${size} KB`;
    }

    return `${(size / 1024).toFixed(1)} MB`;
}
</script>

<template>
    <Head :title="`Edit Materi - ${props.materi.judul}`" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6 flex items-center gap-3">
                <Link
                    :href="route('materi.index')"
                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-800"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </Link>

                <div>
                    <h1
                        class="text-xl font-bold text-slate-800 dark:text-white"
                    >
                        Edit Materi
                    </h1>

                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Perbarui informasi materi pembelajaran
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <!-- INFORMASI -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2
                        class="mb-5 font-semibold text-slate-800 dark:text-white"
                    >
                        Informasi Materi
                    </h2>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Pelatihan
                            </label>

                            <select
                                v-model="form.pelatihan_id"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">Pilih Pelatihan</option>

                                <option
                                    v-for="item in props.pelatihans"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.nama }}
                                </option>
                            </select>

                            <p
                                v-if="form.errors.pelatihan_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.pelatihan_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Kelas
                            </label>

                            <select
                                v-model="form.kelas_id"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">Pilih Kelas</option>

                                <option
                                    v-for="item in props.kelas"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.nama }}
                                </option>
                            </select>

                            <p
                                v-if="form.errors.kelas_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.kelas_id }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Tahap
                            </label>

                            <select
                                v-model="form.tahap_id"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                                <option value="">Tanpa Tahap</option>

                                <option
                                    v-for="item in props.tahaps"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.nama }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Urutan
                            </label>

                            <input
                                v-model.number="form.urutan"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>
                    </div>
                </div>

                <!-- ISI -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2
                        class="mb-5 font-semibold text-slate-800 dark:text-white"
                    >
                        Isi Materi
                    </h2>

                    <div class="space-y-5">

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Judul Materi
                            </label>

                            <input
                                v-model="form.judul"
                                type="text"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.judul"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.judul }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Deskripsi
                            </label>

                            <textarea
                                v-model="form.deskripsi"
                                rows="3"
                                class="w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Isi Materi
                            </label>

                            <textarea
                                v-model="form.isi_materi"
                                rows="12"
                                class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>
                    </div>
                </div>

                <!-- STATUS -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2
                        class="mb-4 font-semibold text-slate-800 dark:text-white"
                    >
                        Status
                    </h2>

                    <div class="flex gap-3">
                        <label
                            class="cursor-pointer rounded-xl border px-5 py-3"
                            :class="
                                form.status === 'draft'
                                    ? 'border-amber-400 bg-amber-50 dark:border-amber-500 dark:bg-amber-500/10'
                                    : 'border-slate-200 dark:border-slate-700'
                            "
                        >
                            <input
                                v-model="form.status"
                                type="radio"
                                value="draft"
                                class="sr-only"
                            />

                            <span
                                class="text-sm font-semibold text-slate-700 dark:text-slate-200"
                            >
                                Draft
                            </span>
                        </label>

                        <label
                            class="cursor-pointer rounded-xl border px-5 py-3"
                            :class="
                                form.status === 'published'
                                    ? 'border-emerald-400 bg-emerald-50 dark:border-emerald-500 dark:bg-emerald-500/10'
                                    : 'border-slate-200 dark:border-slate-700'
                            "
                        >
                            <input
                                v-model="form.status"
                                type="radio"
                                value="published"
                                class="sr-only"
                            />

                            <span
                                class="text-sm font-semibold text-slate-700 dark:text-slate-200"
                            >
                                Published
                            </span>
                        </label>
                    </div>
                </div>

                <!-- FILE -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="mb-5">
                        <h2
                            class="font-semibold text-slate-800 dark:text-white"
                        >
                            File Materi
                        </h2>

                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Upload PDF, Word, PowerPoint, Excel, gambar, video,
                            ZIP atau RAR.
                        </p>
                    </div>

                    <div
                        class="rounded-xl border-2 border-dashed border-slate-300 p-6 text-center dark:border-slate-700"
                    >
                        <input
                            ref="fileInput"
                            type="file"
                            class="mx-auto block w-full max-w-md text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100 dark:file:bg-indigo-500/10 dark:file:text-indigo-400"
                            @change="
                                fileForm.file =
                                    ($event.target as HTMLInputElement)
                                        .files?.[0] ?? null
                            "
                        />

                        <button
                            type="button"
                            class="mt-4 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                            :disabled="
                                !fileForm.file || fileForm.processing
                            "
                            @click="uploadFile"
                        >
                            {{
                                fileForm.processing
                                    ? 'Mengunggah...'
                                    : 'Upload File'
                            }}
                        </button>

                        <p
                            v-if="fileForm.errors.file"
                            class="mt-2 text-xs text-red-500"
                        >
                            {{ fileForm.errors.file }}
                        </p>
                    </div>

                    <!-- FILE LIST -->
                    <div
                        v-if="props.materi.files?.length"
                        class="mt-5 divide-y divide-slate-100 rounded-xl border border-slate-200 dark:divide-slate-800 dark:border-slate-700"
                    >
                        <div
                            v-for="file in props.materi.files"
                            :key="file.id"
                            class="flex items-center justify-between gap-4 p-4"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L18 9.828a4 4 0 10-5.657-5.657L5.05 11.464a6 6 0 108.485 8.485L19 14.485"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p
                                        class="truncate text-sm font-medium text-slate-700 dark:text-slate-200"
                                    >
                                        {{ file.nama_file }}
                                    </p>

                                    <p
                                        class="text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        {{ file.tipe_file ?? 'File' }}
                                        ·
                                        {{ formatSize(file.ukuran) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <a
                                    :href="`/storage/${file.file_path}`"
                                    target="_blank"
                                    class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:hover:bg-slate-800"
                                    title="Buka"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M14 4h6v6M20 4l-9 9"
                                        />
                                    </svg>
                                </a>

                                <button
                                    type="button"
                                    class="rounded-lg p-2 text-slate-500 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10"
                                    title="Hapus"
                                    @click="deleteFile(file.id)"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M6 7h12M10 11v6M14 11v6M9 7V4h6v3M5 7l1 13h12l1-13"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="mt-5 rounded-xl bg-slate-50 p-5 text-center text-sm text-slate-500 dark:bg-slate-800/50 dark:text-slate-400"
                    >
                        Belum ada file materi.
                    </div>
                </div>

                <!-- ACTION -->
                <div class="flex justify-end gap-3">
                    <Link
                        :href="route('materi.index')"
                        class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
                    >
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