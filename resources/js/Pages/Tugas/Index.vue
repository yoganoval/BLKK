<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import {
    Plus,
    Search,
    Pencil,
    Trash2,
    Eye,
    ClipboardList,
    BookOpen,
    GraduationCap,
    Layers,
} from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Materi {
    id: number
    judul: string
}

interface Relasi {
    id: number
    nama?: string
    judul?: string
}

interface Tugas {
    id: number
    pelatihan_id: number
    kelas_id: number
    tahap_id: number
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

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedTugas {
    data: Tugas[]
    current_page: number
    last_page: number
    from: number | null
    to: number | null
    total: number
    links: PaginationLink[]
}

const props = defineProps<{
    tugas: PaginatedTugas
    filters: {
        search?: string
        materi_id?: number | string | null
    }
    materis: Materi[]
}>()

const search = ref(props.filters.search ?? '')
const materiId = ref(
    props.filters.materi_id ? String(props.filters.materi_id) : ''
)

let searchTimeout: ReturnType<typeof setTimeout> | null = null

watch([search, materiId], () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout)
    }

    searchTimeout = setTimeout(() => {
        router.get(
            route('tugas.index'),
            {
                search: search.value || undefined,
                materi_id: materiId.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        )
    }, 400)
})

const hapus = (tugas: Tugas) => {
    if (
        confirm(
            `Yakin ingin menghapus tugas "${tugas.judul}"?`
        )
    ) {
        router.delete(route('tugas.destroy', tugas.id))
    }
}

const formatDeadline = (date?: string | null) => {
    if (!date) return '-'

    return new Date(date).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const statusClass = (status: Tugas['status']) => {
    if (status === 'published') {
        return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
    }

    if (status === 'closed') {
        return 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400'
    }

    return 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'
}
</script>

<template>
    <Head title="Tugas" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- Header -->
            <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                        >
                            <ClipboardList class="h-6 w-6" />
                        </div>

                        <div>
                            <h1
                                class="text-2xl font-bold text-slate-900 dark:text-white"
                            >
                                Tugas
                            </h1>

                            <p
                                class="text-sm text-slate-500 dark:text-slate-400"
                            >
                                Kelola tugas peserta pelatihan
                            </p>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('tugas.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                >
                    <Plus class="h-4 w-4" />
                    Tambah Tugas
                </Link>
            </div>

            <!-- Filter -->
            <div
                class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="grid gap-4 md:grid-cols-2">

                    <!-- Search -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                        >
                            Cari tugas
                        </label>

                        <div class="relative">
                            <Search
                                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari berdasarkan judul..."
                                class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>
                    </div>

                    <!-- Materi -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                        >
                            Filter Materi
                        </label>

                        <select
                            v-model="materiId"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                            <option value="">
                                Semua Materi
                            </option>

                            <option
                                v-for="materi in materis"
                                :key="materi.id"
                                :value="String(materi.id)"
                            >
                                {{ materi.judul }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/50"
                        >
                            <tr>
                                <th class="px-5 py-4 font-semibold">
                                    Tugas
                                </th>

                                <th class="px-5 py-4 font-semibold">
                                    Materi
                                </th>

                                <th class="px-5 py-4 font-semibold">
                                    Kelas
                                </th>

                                <th class="px-5 py-4 font-semibold">
                                    Deadline
                                </th>

                                <th class="px-5 py-4 font-semibold">
                                    Nilai
                                </th>

                                <th class="px-5 py-4 font-semibold">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-right font-semibold">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-slate-200 dark:divide-slate-800"
                        >
                            <tr
                                v-for="item in tugas.data"
                                :key="item.id"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >
                                <!-- Tugas -->
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900 dark:text-white">
                                        {{ item.judul }}
                                    </div>

                                    <div
                                        v-if="item.deskripsi"
                                        class="mt-1 max-w-xs truncate text-xs text-slate-500"
                                    >
                                        {{ item.deskripsi }}
                                    </div>
                                </td>

                                <!-- Materi -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <BookOpen
                                            class="h-4 w-4 text-indigo-500"
                                        />

                                        <span>
                                            {{ item.materi?.judul ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Kelas -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <GraduationCap
                                            class="h-4 w-4 text-emerald-500"
                                        />

                                        <span>
                                            {{ item.kelas?.nama ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Deadline -->
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ formatDeadline(item.deadline) }}
                                </td>

                                <!-- Nilai -->
                                <td class="px-5 py-4">
                                    {{ item.nilai_maksimal }}
                                </td>

                                <!-- Status -->
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="statusClass(item.status)"
                                    >
                                        {{
                                            item.status === 'published'
                                                ? 'Published'
                                                : item.status === 'closed'
                                                  ? 'Closed'
                                                  : 'Draft'
                                        }}
                                    </span>
                                </td>

                                <!-- Action -->
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-1">

                                        <Link
                                            :href="route('tugas.show', item.id)"
                                            class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:hover:bg-slate-800"
                                            title="Detail"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Link>

                                        <Link
                                            :href="route('tugas.edit', item.id)"
                                            class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-amber-600 dark:hover:bg-slate-800"
                                            title="Edit"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Link>

                                        <button
                                            type="button"
                                            class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10"
                                            title="Hapus"
                                            @click="hapus(item)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>

                                    </div>
                                </td>
                            </tr>

                            <!-- Empty -->
                            <tr v-if="tugas.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-5 py-12 text-center"
                                >
                                    <ClipboardList
                                        class="mx-auto mb-3 h-10 w-10 text-slate-300"
                                    />

                                    <p class="font-medium text-slate-600 dark:text-slate-300">
                                        Belum ada tugas
                                    </p>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Silakan tambahkan tugas baru.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="tugas.last_page > 1"
                    class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800"
                >
                    <p class="text-sm text-slate-500">
                        Menampilkan
                        {{ tugas.from ?? 0 }}
                        -
                        {{ tugas.to ?? 0 }}
                        dari
                        {{ tugas.total }}
                        tugas
                    </p>

                    <div class="flex flex-wrap gap-1">
                        <template
                            v-for="(link, index) in tugas.links"
                            :key="index"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                class="rounded-lg px-3 py-1.5 text-sm transition"
                                :class="
                                    link.active
                                        ? 'bg-indigo-600 text-white'
                                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                                "
                                v-html="link.label"
                            />

                            <span
                                v-else
                                class="rounded-lg px-3 py-1.5 text-sm text-slate-300"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>




