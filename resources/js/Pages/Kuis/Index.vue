<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
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
}

interface Creator {
    id: number
    name: string
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
    soal_count: number
    program_pelatihan?: ProgramPelatihan
    kelas?: Kelas
    materi?: Materi | null
    creator?: Creator | null
    created_at?: string
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedKuis {
    data: Kuis[]
    links: PaginationLink[]
    current_page: number
    last_page: number
    total: number
    from: number | null
    to: number | null
}

const props = defineProps<{
    kuis: PaginatedKuis
    filters: {
        search?: string
        status?: string
    }
}>()

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')
const deletingId = ref<number | null>(null)

let searchTimer: ReturnType<typeof setTimeout> | undefined

function applyFilters() {
    router.get(
        '/kuis',
        {
            search: search.value || undefined,
            status: status.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    )
}

watch(search, () => {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(applyFilters, 350)
})

watch(status, applyFilters)

function deleteKuis(item: Kuis) {
    if (
        !window.confirm(
            `Yakin ingin menghapus kuis "${item.judul}"? Tindakan ini tidak dapat dibatalkan.`,
        )
    ) {
        return
    }

    deletingId.value = item.id

    router.delete(`/kuis/${item.id}`, {
        preserveScroll: true,
        onFinish: () => {
            deletingId.value = null
        },
    })
}

function statusLabel(value: Kuis['status']) {
    const labels = {
        draft: 'Draft',
        published: 'Dipublikasikan',
        closed: 'Ditutup',
    }

    return labels[value]
}

function statusClass(value: Kuis['status']) {
    const classes = {
        draft: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        published: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        closed: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    }

    return classes[value]
}

function programName(item: Kuis) {
    return (
        item.program_pelatihan?.nama ??
        item.program_pelatihan?.judul ??
        `Program #${item.program_pelatihan_id}`
    )
}
</script>

<template>
    <Head title="Manajemen Kuis" />

    <div class="space-y-6 p-4 sm:p-6">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <div class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Akademik</span>
                    <span>/</span>
                    <span class="text-blue-600 dark:text-blue-400">Manajemen Kuis</span>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    Manajemen Kuis
                </h1>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Kelola kuis, soal, durasi pengerjaan, dan pengaturan nilai peserta.
                </p>
            </div>

            <Link
                href="/kuis/create"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
                <span class="text-lg leading-none">+</span>
                Tambah Kuis
            </Link>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Kuis
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ kuis.total }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-xl text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        <span>Q</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Kuis Ditampilkan
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ kuis.data.length }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-xl text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                        <span>✓</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Jumlah Soal di Halaman
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ kuis.data.reduce((sum, item) => sum + item.soal_count, 0) }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-xl text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">
                        <span>≡</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <!-- Search and filters -->
            <div class="flex flex-col gap-3 border-b border-gray-200 p-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">
                        Daftar Kuis
                    </h2>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Cari kuis berdasarkan judul atau deskripsi.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative">
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Cari kuis..."
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-900 sm:w-64"
                        />
                    </div>

                    <select
                        v-model="status"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200"
                    >
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="published">Dipublikasikan</option>
                        <option value="closed">Ditutup</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 dark:bg-gray-900/60 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-4 font-semibold">Kuis</th>
                            <th class="px-5 py-4 font-semibold">Program Pelatihan</th>
                            <th class="px-5 py-4 font-semibold">Kelas</th>
                            <th class="px-5 py-4 text-center font-semibold">Soal</th>
                            <th class="px-5 py-4 font-semibold">Pengaturan</th>
                            <th class="px-5 py-4 font-semibold">Status</th>
                            <th class="px-5 py-4 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr
                            v-for="item in kuis.data"
                            :key="item.id"
                            class="transition hover:bg-gray-50/80 dark:hover:bg-gray-700/30"
                        >
                            <td class="px-5 py-5">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ item.judul.charAt(0).toUpperCase() }}
                                    </div>

                                    <div class="max-w-xs">
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ item.judul }}
                                        </p>
                                        <p class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">
                                            {{ item.deskripsi || 'Tidak ada deskripsi' }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-400">
                                            ID: {{ item.id }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-5 text-gray-600 dark:text-gray-300">
                                {{ programName(item) }}
                            </td>

                            <td class="px-5 py-5 text-gray-600 dark:text-gray-300">
                                {{ item.kelas?.nama ?? `Kelas #${item.kelas_id}` }}
                            </td>

                            <td class="px-5 py-5 text-center">
                                <span class="inline-flex min-w-10 items-center justify-center rounded-lg bg-violet-50 px-3 py-1.5 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                                    {{ item.soal_count }}
                                </span>
                            </td>

                            <td class="px-5 py-5">
                                <div class="space-y-1.5 text-xs text-gray-600 dark:text-gray-300">
                                    <p>
                                        <span class="text-gray-400">Durasi:</span>
                                        {{ item.waktu_menit ? `${item.waktu_menit} menit` : 'Tidak dibatasi' }}
                                    </p>
                                    <p>
                                        <span class="text-gray-400">Nilai lulus:</span>
                                        {{ item.nilai_lulus }}
                                    </p>
                                    <p>
                                        <span class="text-gray-400">Percobaan:</span>
                                        {{ item.percobaan_maksimal }} kali
                                    </p>
                                </div>
                            </td>

                            <td class="px-5 py-5">
                                <span
                                    class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="statusClass(item.status)"
                                >
                                    {{ statusLabel(item.status) }}
                                </span>
                            </td>

                            <td class="px-5 py-5">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Kelola soal -->
                                    <Link
                                        :href="`/kuis/${item.id}/soal`"
                                        class="rounded-lg border border-violet-200 px-3 py-2 text-xs font-semibold text-violet-700 hover:bg-violet-50"
                                    >
                                        Kelola Soal
                                    </Link>

                                    <!-- Edit kuis -->
                                    <Link
                                        :href="`/kuis/${item.id}/edit`"
                                        class="rounded-lg border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50"
                                    >
                                        Edit
                                    </Link>

                                    <!-- Hapus kuis -->
                                    <button
                                        type="button"
                                        class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50"
                                        @click="deleteKuis(item)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="kuis.data.length === 0">
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-2xl text-gray-400 dark:bg-gray-700">
                                    ?
                                </div>

                                <h3 class="mt-4 font-semibold text-gray-900 dark:text-white">
                                    Kuis tidak ditemukan
                                </h3>

                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Tambahkan kuis baru atau ubah pencarian dan filter.
                                </p>

                                <Link
                                    href="/kuis/create"
                                    class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                                >
                                    + Tambah Kuis
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col gap-4 border-t border-gray-200 px-5 py-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Menampilkan {{ kuis.from ?? 0 }}–{{ kuis.to ?? 0 }}
                    dari {{ kuis.total }} kuis
                </p>

                <div class="flex flex-wrap items-center gap-1">
                    <template v-for="(link, index) in kuis.links" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            preserve-scroll
                            class="rounded-lg border px-3 py-2 text-xs font-medium transition"
                            :class="link.active
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'"
                        />

                        <span
                            v-else
                            v-html="link.label"
                            class="cursor-not-allowed rounded-lg border border-gray-100 px-3 py-2 text-xs text-gray-300 dark:border-gray-700 dark:text-gray-600"
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
