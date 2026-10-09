<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Peserta {
    id: number;
    nama: string;
}

interface PengumpulanTugas {
    id: number;
    tugas_id: number;
    peserta_id: number;
    nama_file: string | null;
    file_path: string | null;
    tipe_file: string | null;
    ukuran: number | null;
    catatan: string | null;
    tanggal_kumpul: string | null;
    nilai: number | null;
    umpan_balik: string | null;
    status: string;
    peserta?: Peserta;
}

interface Materi {
    id: number;
    judul: string;
    deskripsi: string | null;
    pelatihan?: {
        id: number;
        nama: string;
    };
    kelas?: {
        id: number;
        nama: string;
    };
    tahap?: {
        id: number;
        nama: string;
    };
}

interface Tugas {
    id: number;
    materi_id: number;
    judul: string;
    deskripsi: string | null;
    instruksi: string | null;
    tanggal_mulai: string | null;
    tanggal_deadline: string | null;
    nilai_maksimal: number;
    status: 'draft' | 'published' | 'closed';
    materi?: Materi;
    pengumpulan_tugas?: PengumpulanTugas[];
}

interface AuthUser {
    id: number;
    name: string;
    roles?: string[];
    permissions?: string[];
}

const props = defineProps<{
    tugas: Tugas;
}>();

const page = usePage();

const auth = computed<AuthUser>(() => {
    return page.props.auth?.user as AuthUser;
});

const roles = computed(() => auth.value?.roles ?? []);

const isPeserta = computed(() => roles.value.includes('peserta'));
const isInstruktur = computed(() => roles.value.includes('instruktur'));
const isAdmin = computed(() => roles.value.includes('admin'));

const mySubmission = computed(() => {
    if (!props.tugas.pengumpulan_tugas || !auth.value) {
        return null;
    }

    return (
        props.tugas.pengumpulan_tugas.find(
            (item) => item.peserta_id === auth.value.id,
        ) ?? null
    );
});

const submissions = computed(() => {
    return props.tugas.pengumpulan_tugas ?? [];
});

const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    file: null as File | null,
    catatan: '',
});

function selectFile(event: Event) {
    const target = event.target as HTMLInputElement;

    if (target.files && target.files.length > 0) {
        form.file = target.files[0];
    }
}

function submitTugas() {
    form.post(route('pengumpulan-tugas.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
}

function formatDate(date: string | null) {
    if (!date) return '-';

    return new Date(date).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatFileSize(size: number | null) {
    if (!size) return '-';

    if (size < 1024) {
        return `${size} KB`;
    }

    return `${(size / 1024).toFixed(2)} MB`;
}

function statusClass(status: string) {
    switch (status) {
        case 'published':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400';

        case 'closed':
            return 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400';

        case 'draft':
            return 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';

        case 'dinilai':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400';

        case 'revisi':
            return 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400';

        default:
            return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
    }
}

function statusLabel(status: string) {
    const labels: Record<string, string> = {
        published: 'Dipublikasikan',
        closed: 'Ditutup',
        draft: 'Draft',
        dikumpulkan: 'Dikumpulkan',
        dinilai: 'Sudah Dinilai',
        revisi: 'Perlu Revisi',
    };

    return labels[status] ?? status;
}
</script>

<template>
    <Head :title="tugas.judul" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M9 5h6M9 3h6a2 2 0 012 2v1h1a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h1V5a2 2 0 012-2z"
                                />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                                {{ tugas.judul }}
                            </h1>

                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Detail tugas dan pengumpulan peserta
                            </p>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('tugas.index')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
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

                    Kembali
                </Link>
            </div>

            <!-- INFORMASI TUGAS -->
            <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- DETAIL -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">
                            Informasi Tugas
                        </h2>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="statusClass(tugas.status)"
                        >
                            {{ statusLabel(tugas.status) }}
                        </span>
                    </div>

                    <div class="space-y-5">

                        <div v-if="tugas.materi">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Materi
                            </p>

                            <Link
                                :href="route('materi.show', tugas.materi.id)"
                                class="mt-1 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                            >
                                {{ tugas.materi.judul }}
                            </Link>
                        </div>

                        <div
                            v-if="tugas.materi?.pelatihan || tugas.materi?.kelas || tugas.materi?.tahap"
                            class="grid grid-cols-1 gap-4 sm:grid-cols-3"
                        >
                            <div v-if="tugas.materi?.pelatihan">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Pelatihan
                                </p>

                                <p class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200">
                                    {{ tugas.materi.pelatihan.nama }}
                                </p>
                            </div>

                            <div v-if="tugas.materi?.kelas">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Kelas
                                </p>

                                <p class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200">
                                    {{ tugas.materi.kelas.nama }}
                                </p>
                            </div>

                            <div v-if="tugas.materi?.tahap">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Tahap
                                </p>

                                <p class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200">
                                    {{ tugas.materi.tahap.nama }}
                                </p>
                            </div>
                        </div>

                        <div v-if="tugas.deskripsi">
                            <p class="mb-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Deskripsi
                            </p>

                            <div class="whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-slate-400">
                                {{ tugas.deskripsi }}
                            </div>
                        </div>

                        <div v-if="tugas.instruksi">
                            <p class="mb-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Instruksi Pengerjaan
                            </p>

                            <div class="whitespace-pre-line rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                {{ tugas.instruksi }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INFORMASI WAKTU -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="mb-5 text-lg font-bold text-slate-800 dark:text-white">
                        Waktu & Penilaian
                    </h2>

                    <div class="space-y-4">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Mulai
                            </p>

                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">
                                {{ formatDate(tugas.tanggal_mulai) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Deadline
                            </p>

                            <p class="mt-1 text-sm font-semibold text-red-600 dark:text-red-400">
                                {{ formatDate(tugas.tanggal_deadline) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Nilai Maksimal
                            </p>

                            <p class="mt-1 text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                {{ tugas.nilai_maksimal }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- PESERTA -->
            <!-- ========================================== -->

            <div
                v-if="isPeserta"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">
                        Pengumpulan Tugas
                    </h2>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Upload hasil pengerjaan tugas kamu.
                    </p>
                </div>

                <!-- SUDAH DIKUMPULKAN -->
                <div
                    v-if="mySubmission"
                    class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10"
                >
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-emerald-600 dark:text-emerald-400">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>

                        <div>
                            <p class="font-semibold text-emerald-800 dark:text-emerald-300">
                                Tugas sudah dikumpulkan
                            </p>

                            <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-400">
                                Dikumpulkan:
                                {{ formatDate(mySubmission.tanggal_kumpul) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FORM -->
                <form
                    @submit.prevent="submitTugas"
                    class="space-y-5"
                >
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            File Tugas
                        </label>

                        <input
                            ref="fileInput"
                            type="file"
                            @change="selectFile"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-600 file:mr-4 file:border-0 file:bg-indigo-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        />

                        <p class="mt-2 text-xs text-slate-400">
                            Maksimal 50 MB.
                        </p>

                        <p
                            v-if="form.errors.file"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ form.errors.file }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Catatan
                        </label>

                        <textarea
                            v-model="form.catatan"
                            rows="4"
                            placeholder="Tambahkan catatan jika diperlukan..."
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        ></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing || !form.file"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
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
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12V4m0 0L8 8m4-4l4 4"
                                />
                            </svg>

                            {{
                                form.processing
                                    ? 'Mengirim...'
                                    : mySubmission
                                      ? 'Kumpulkan Ulang'
                                      : 'Kumpulkan Tugas'
                            }}
                        </button>
                    </div>
                </form>

                <!-- HASIL PENILAIAN -->
                <div
                    v-if="mySubmission?.nilai !== null"
                    class="mt-6 rounded-xl border border-indigo-200 bg-indigo-50 p-5 dark:border-indigo-500/20 dark:bg-indigo-500/10"
                >
                    <p class="text-sm font-semibold text-indigo-800 dark:text-indigo-300">
                        Hasil Penilaian
                    </p>

                    <div class="mt-2 flex items-end gap-2">
                        <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                            {{ mySubmission?.nilai }}
                        </span>

                        <span class="mb-1 text-sm text-slate-500">
                            / {{ tugas.nilai_maksimal }}
                        </span>
                    </div>

                    <div
                        v-if="mySubmission?.umpan_balik"
                        class="mt-4"
                    >
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Feedback Instruktur
                        </p>

                        <p class="mt-1 whitespace-pre-line text-sm text-slate-600 dark:text-slate-400">
                            {{ mySubmission.umpan_balik }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- INSTRUKTUR / ADMIN -->
            <!-- ========================================== -->

            <div
                v-if="isInstruktur || isAdmin"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">
                            Pengumpulan Peserta
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Daftar peserta yang telah mengumpulkan tugas.
                        </p>
                    </div>

                    <div class="rounded-xl bg-indigo-50 px-4 py-2 dark:bg-indigo-500/10">
                        <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ submissions.length }} Pengumpulan
                        </span>
                    </div>
                </div>

                <!-- EMPTY -->
                <div
                    v-if="submissions.length === 0"
                    class="rounded-xl border border-dashed border-slate-300 p-10 text-center dark:border-slate-700"
                >
                    <svg
                        class="mx-auto h-10 w-10 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M9 13h6m-6 4h6M8 3h8l4 4v14H4V3h4z"
                        />
                    </svg>

                    <p class="mt-3 text-sm font-medium text-slate-600 dark:text-slate-300">
                        Belum ada peserta yang mengumpulkan tugas.
                    </p>
                </div>

                <!-- TABLE -->
                <div
                    v-else
                    class="overflow-x-auto"
                >
                    <table class="w-full min-w-[900px] text-left">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700">
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Peserta
                                </th>

                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    File
                                </th>

                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Dikumpulkan
                                </th>

                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Status
                                </th>

                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Nilai
                                </th>

                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="submission in submissions"
                                :key="submission.id"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                            >
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-slate-800 dark:text-white">
                                        {{ submission.peserta?.nama ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-4 py-4">
                                    <a
                                        v-if="submission.file_path"
                                        :href="`/storage/${submission.file_path}`"
                                        target="_blank"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                                    >
                                        {{ submission.nama_file }}
                                    </a>

                                    <span
                                        v-else
                                        class="text-sm text-slate-400"
                                    >
                                        Tidak ada file
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ formatDate(submission.tanggal_kumpul) }}
                                </td>

                                <td class="px-4 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="statusClass(submission.status)"
                                    >
                                        {{ statusLabel(submission.status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    <span
                                        v-if="submission.nilai !== null"
                                        class="font-bold text-indigo-600 dark:text-indigo-400"
                                    >
                                        {{ submission.nilai }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-sm text-slate-400"
                                    >
                                        Belum dinilai
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-right">
                                    <Link
                                        :href="route('pengumpulan-tugas.show', submission.id)"
                                        class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                    >
                                        Detail
                                    </Link>

                                    <Link
                                        :href="route('pengumpulan-tugas.edit', submission.id)"
                                        class="ml-2 inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20"
                                    >
                                        Nilai
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</template>