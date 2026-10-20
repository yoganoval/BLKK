<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

interface Pelatihan {
    nama: string;
}

interface Tahap {
    nama: string;
}

interface ProgramPelatihan {
    id: number;
    nama?: string;
    pelatihan?: Pelatihan;
    tahap?: Tahap;
}

interface Kelas {
    id: number;
    nama: string;
    durasi_hari?: number;
}

interface Pembuat {
    name?: string;
    nama?: string;
}

interface Tugas {
    id: number;
    judul: string;
    instruksi?: string | null;
    deadline?: string | null;
    nilai_maksimal: number;
    status: 'draft' | 'published' | 'closed';
    program_pelatihan_id: number;
    kelas_id: number;
    program_pelatihan?: ProgramPelatihan;
    programPelatihan?: ProgramPelatihan;
    kelas?: Kelas;
    pembuat?: Pembuat;
    created_at?: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTugas {
    data: Tugas[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

interface Filters {
    search?: string;
    program_pelatihan_id?: string | number | null;
    kelas_id?: string | number | null;
    status?: string | null;
}

const props = defineProps<{
    tugas: PaginatedTugas;
    filters: Filters;
    programPelatihans: ProgramPelatihan[];
    kelass: Kelas[];
}>();

const search = ref(props.filters.search ?? '');
const programId = ref(
    props.filters.program_pelatihan_id?.toString() ?? ''
);
const kelasId = ref(props.filters.kelas_id?.toString() ?? '');
const status = ref(props.filters.status ?? '');

let searchTimer: ReturnType<typeof setTimeout> | undefined;

function filterData() {
    router.get(
        route('tugas.index'),
        {
            search: search.value || undefined,
            program_pelatihan_id: programId.value || undefined,
            kelas_id: kelasId.value || undefined,
            status: status.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}

watch(search, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(filterData, 350);
});

watch([programId, kelasId, status], filterData);

function hapusTugas(t: Tugas) {
    if (!confirm(`Yakin ingin menghapus tugas "${t.judul}"?`)) {
        return;
    }

    router.delete(route('tugas.destroy', { tugas: t.id }), {
        preserveScroll: true,
    });
}

function namaProgram(t: Tugas) {
    const program = t.programPelatihan ?? t.program_pelatihan;

    if (!program) return '-';

    return [
        program.pelatihan?.nama ?? program.nama,
        program.tahap?.nama,
    ].filter(Boolean).join(' - ') || '-';
}

function tanggal(value?: string | null) {
    if (!value) return 'Tidak ditentukan';

    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(d);
}

function statusClass(value: string) {
    return {
        draft: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        published: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        closed: 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
    }[value] ?? 'bg-slate-100 text-slate-600';
}
</script>

<template>
    <Head title="Manajemen Tugas" />

    <div class="space-y-6 p-4 sm:p-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                    Manajemen Tugas
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola tugas, instruksi, tenggat waktu, dan status publikasi.
                </p>
            </div>

            <Link
                :href="route('tugas.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
            >
                <span class="text-lg">+</span>
                Tambah Tugas
            </Link>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">Total tugas</p>
                <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-white">
                    {{ tugas.total }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">Draft</p>
                <p class="mt-2 text-2xl font-bold text-amber-600">
                    {{ tugas.data.filter(t => t.status === 'draft').length }}
                </p>
                <p class="mt-1 text-xs text-slate-400">Pada halaman ini</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">Dipublikasikan</p>
                <p class="mt-2 text-2xl font-bold text-emerald-600">
                    {{ tugas.data.filter(t => t.status === 'published').length }}
                </p>
                <p class="mt-1 text-xs text-slate-400">Pada halaman ini</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">Ditutup</p>
                <p class="mt-2 text-2xl font-bold text-slate-600 dark:text-slate-300">
                    {{ tugas.data.filter(t => t.status === 'closed').length }}
                </p>
                <p class="mt-1 text-xs text-slate-400">Pada halaman ini</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mb-4">
                <h2 class="font-semibold text-slate-800 dark:text-white">
                    Filter dan Pencarian
                </h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Cari judul tugas atau pilih program, kelas, dan status.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                <input
                    v-model="search"
                    type="search"
                    placeholder="Cari judul tugas..."
                    class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                />

                <select
                    v-model="programId"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                >
                    <option value="">Semua program pelatihan</option>
                    <option
                        v-for="p in programPelatihans"
                        :key="p.id"
                        :value="String(p.id)"
                    >
                        {{ p.pelatihan?.nama ?? p.nama ?? `Program #${p.id}` }}
                        {{ p.tahap?.nama ? ` - ${p.tahap.nama}` : '' }}
                    </option>
                </select>

                <select
                    v-model="kelasId"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                >
                    <option value="">Semua kelas</option>
                    <option v-for="k in kelass" :key="k.id" :value="String(k.id)">
                        {{ k.nama }}
                    </option>
                </select>

                <select
                    v-model="status"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-indigo-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                >
                    <option value="">Semua status</option>
                    <option value="draft">Draft</option>
                    <option value="published">Dipublikasikan</option>
                    <option value="closed">Ditutup</option>
                </select>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex flex-col justify-between gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center dark:border-slate-700">
                <h2 class="font-semibold text-slate-800 dark:text-white">
                    Daftar Tugas
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Menampilkan {{ tugas.from ?? 0 }}–{{ tugas.to ?? 0 }}
                    dari {{ tugas.total }} tugas
                </p>
            </div>

            <div v-if="tugas.data.length" class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/60 dark:text-slate-400">
                        <tr>
                            <th class="px-5 py-4">Tugas</th>
                            <th class="px-5 py-4">Program</th>
                            <th class="px-5 py-4">Kelas</th>
                            <th class="px-5 py-4">Deadline</th>
                            <th class="px-5 py-4">Nilai Maksimal</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr
                            v-for="t in tugas.data"
                            :key="t.id"
                            class="transition hover:bg-slate-50/80 dark:hover:bg-slate-700/30"
                        >
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800 dark:text-white">
                                    {{ t.judul }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    ID tugas: #{{ t.id }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                {{ namaProgram(t) }}
                            </td>

                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                {{ t.kelas?.nama ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                {{ tanggal(t.deadline) }}
                            </td>

                            <td class="px-5 py-4 font-semibold text-slate-700 dark:text-slate-200">
                                {{ Number(t.nilai_maksimal).toLocaleString('id-ID') }}
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="statusClass(t.status)"
                                >
                                    {{
                                        t.status === 'published'
                                            ? 'Dipublikasikan'
                                            : t.status === 'closed'
                                                ? 'Ditutup'
                                                : 'Draft'
                                    }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="route('tugas.show', { tuga: t.id })"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700"
                                    >
                                        Detail
                                    </Link>

                                    <Link
                                        :href="route('tugas.edit', { tuga: t.id })"
                                        class="rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        @click="hapusTugas(t)"
                                        class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="px-5 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl dark:bg-slate-700">
                    📝
                </div>
                <h3 class="mt-4 font-semibold text-slate-800 dark:text-white">
                    Belum ada tugas
                </h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Coba ubah filter atau tambahkan tugas baru.
                </p>
                <Link
                    :href="route('tugas.create')"
                    class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    Tambah Tugas
                </Link>
            </div>

            <div
                v-if="tugas.links.length > 3"
                class="flex flex-wrap items-center justify-center gap-2 border-t border-slate-200 px-4 py-4 dark:border-slate-700"
            >
                <template v-for="(link, index) in tugas.links" :key="index">
                    <button
                        v-if="link.url"
                        type="button"
                        @click="router.visit(link.url, { preserveScroll: true })"
                        :disabled="link.active"
                        class="min-w-10 rounded-lg border px-3 py-2 text-sm"
                        :class="link.active
                            ? 'border-indigo-600 bg-indigo-600 text-white'
                            : 'border-slate-200 text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700'"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="min-w-10 rounded-lg px-3 py-2 text-center text-sm text-slate-400"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
