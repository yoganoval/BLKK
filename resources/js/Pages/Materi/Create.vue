
<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

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

interface Props {
    pelatihans: Pelatihan[];
    kelas: Kelas[];
    tahaps: Tahap[];
}

const props = defineProps<Props>();

const form = useForm({
    pelatihan_id: '',
    kelas_id: '',
    tahap_id: '',
    judul: '',
    deskripsi: '',
    isi_materi: '',
    urutan: 1,
    status: 'draft' as 'draft' | 'published',
});

function submit() {
    form.post(route('materi.store'));
}
</script>

<template>
    <Head title="Tambah Materi" />

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
                        Tambah Materi
                    </h1>

                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Tambahkan materi pembelajaran baru
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <!-- INFORMASI MATERI -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2
                        class="mb-5 font-semibold text-slate-800 dark:text-white"
                    >
                        Informasi Materi
                    </h2>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <!-- PELATIHAN -->
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
                                <option value="">
                                    Pilih Pelatihan
                                </option>

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

                        <!-- KELAS -->
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
                                <option value="">
                                    Pilih Kelas
                                </option>

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

                        <!-- TAHAP -->
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
                                <option value="">
                                    Tanpa Tahap
                                </option>

                                <option
                                    v-for="item in props.tahaps"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.nama }}
                                </option>
                            </select>

                            <p
                                v-if="form.errors.tahap_id"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.tahap_id }}
                            </p>
                        </div>

                        <!-- URUTAN -->
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

                            <p
                                v-if="form.errors.urutan"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.urutan }}
                            </p>
                        </div>

                    </div>
                </div>

                <!-- ISI MATERI -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <h2
                        class="mb-5 font-semibold text-slate-800 dark:text-white"
                    >
                        Isi Materi
                    </h2>

                    <div class="space-y-5">

                        <!-- JUDUL -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Judul Materi
                            </label>

                            <input
                                v-model="form.judul"
                                type="text"
                                placeholder="Contoh: Pengenalan Microsoft Word"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p
                                v-if="form.errors.judul"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.judul }}
                            </p>
                        </div>

                        <!-- DESKRIPSI -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Deskripsi
                            </label>

                            <textarea
                                v-model="form.deskripsi"
                                rows="3"
                                placeholder="Masukkan deskripsi singkat materi..."
                                class="w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>

                            <p
                                v-if="form.errors.deskripsi"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.deskripsi }}
                            </p>
                        </div>

                        <!-- ISI MATERI -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Isi Materi
                            </label>

                            <textarea
                                v-model="form.isi_materi"
                                rows="12"
                                placeholder="Tuliskan isi materi pembelajaran di sini..."
                                class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>

                            <p
                                v-if="form.errors.isi_materi"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.isi_materi }}
                            </p>
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

                        <!-- DRAFT -->
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

                        <!-- PUBLISHED -->
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

                    <p
                        v-if="form.errors.status"
                        class="mt-2 text-xs text-red-500"
                    >
                        {{ form.errors.status }}
                    </p>
                </div>

                <!-- INFORMASI UPLOAD -->
                <div
                    class="rounded-2xl border border-indigo-100 bg-indigo-50 p-6 dark:border-indigo-900/50 dark:bg-indigo-500/10"
                >
                    <div class="flex gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400"
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
                                    d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"
                                />
                            </svg>
                        </div>

                        <div>
                            <h2
                                class="font-semibold text-indigo-900 dark:text-indigo-300"
                            >
                                File Materi
                            </h2>

                            <p
                                class="mt-1 text-sm leading-6 text-indigo-700 dark:text-indigo-400"
                            >
                                File materi dapat diupload setelah materi
                                berhasil dibuat. Setelah menyimpan materi,
                                Anda akan diarahkan ke halaman edit untuk
                                mengunggah file PDF, Word, PowerPoint, Excel,
                                gambar, video, ZIP, atau RAR.
                            </p>
                        </div>

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
                                : 'Simpan Materi'
                        }}
                    </button>

                </div>

            </form>
        </div>
    </div>
</template>
