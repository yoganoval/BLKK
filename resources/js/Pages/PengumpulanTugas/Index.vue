<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Tugas {
    id: number
    judul: string
}

interface Peserta {
    id: number
    nama: string
}

interface Pengumpulan {
    id: number
    tugas_id: number
    peserta_id: number
    nama_file?: string
    file_path?: string
    tanggal_kumpul?: string
    nilai?: number
    status: string
    tugas?: Tugas
    peserta?: Peserta
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

const props = defineProps<{
    pengumpulanTugas: {
        data: Pengumpulan[]
        links: PaginationLink[]
        from: number
        total: number
    }
    filters: {
        tugas_id?: number | string
        peserta_id?: number | string
        status?: string
    }
    tugas: Tugas[]
    pesertas: Peserta[]
}>()

const filter = reactive({
    tugas_id: props.filters.tugas_id ?? '',
    peserta_id: props.filters.peserta_id ?? '',
    status: props.filters.status ?? '',
})

const applyFilter = () => {
    router.get(
        route('pengumpulan-tugas.index'),
        {
            tugas_id: filter.tugas_id || undefined,
            peserta_id: filter.peserta_id || undefined,
            status: filter.status || undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    )
}

const resetFilter = () => {
    filter.tugas_id = ''
    filter.peserta_id = ''
    filter.status = ''

    router.get(route('pengumpulan-tugas.index'))
}

const formatDate = (date?: string) => {
    if (!date) return '-'

    return new Date(date).toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    })
}

const deleteData = (id: number) => {
    if (confirm('Hapus pengumpulan tugas ini?')) {
        router.delete(
            route('pengumpulan-tugas.destroy', id),
        )
    }
}
</script>

<template>
    <Head title="Pengumpulan Tugas" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6">
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
                                d="M9 12l2 2 4-4m5-2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                            Pengumpulan Tugas
                        </h1>

                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Kelola pengumpulan dan penilaian tugas peserta
                        </p>
                    </div>
                </div>
            </div>

            <!-- FILTER -->
            <div
                class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="grid gap-4 md:grid-cols-4">
                    <select
                        v-model="filter.tugas_id"
                        class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                        <option value="">Semua Tugas</option>

                        <option
                            v-for="item in props.tugas"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.judul }}
                        </option>
                    </select>

                    <select
                        v-model="filter.peserta_id"
                        class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                        <option value="">Semua Peserta</option>

                        <option
                            v-for="peserta in props.pesertas"
                            :key="peserta.id"
                            :value="peserta.id"
                        >
                            {{ peserta.nama }}
                        </option>
                    </select>

                    <select
                        v-model="filter.status"
                        class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                        <option value="">Semua Status</option>
                        <option value="dikumpulkan">Dikumpulkan</option>
                        <option value="dinilai">Dinilai</option>
                        <option value="revisi">Revisi</option>
                    </select>

                    <div class="flex gap-2">
                        <button
                            class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                            @click="applyFilter"
                        >
                            Filter
                        </button>

                        <button
                            class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold dark:border-slate-700"
                            @click="resetFilter"
                        >
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-slate-50 dark:bg-slate-950"
                        >
                            <tr>
                                <th class="px-6 py-4">#</th>
                                <th class="px-6 py-4">Peserta</th>
                                <th class="px-6 py-4">Tugas</th>
                                <th class="px-6 py-4">File</th>
                                <th class="px-6 py-4">Dikumpulkan</th>
                                <th class="px-6 py-4">Nilai</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-slate-100 dark:divide-slate-800"
                        >
                            <tr
                                v-for="(item, index) in props.pengumpulanTugas.data"
                                :key="item.id"
                            >
                                <td class="px-6 py-4 text-slate-500">
                                    {{ (props.pengumpulanTugas.from ?? 1) + index }}
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">
                                    {{ item.peserta?.nama || '-' }}
                                </td>

                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                    {{ item.tugas?.judul || '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <a
                                        v-if="item.file_path"
                                        :href="`/storage/${item.file_path}`"
                                        target="_blank"
                                        class="font-medium text-indigo-600 hover:underline"
                                    >
                                        {{ item.nama_file || 'Lihat File' }}
                                    </a>

                                    <span
                                        v-else
                                        class="text-slate-400"
                                    >
                                        Tidak ada file
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ formatDate(item.tanggal_kumpul) }}
                                </td>

                                <td class="px-6 py-4 font-semibold">
                                    {{ item.nilai ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="route('pengumpulan-tugas.show', item.id)"
                                            class="rounded-lg px-3 py-2 text-indigo-600 hover:bg-indigo-50"
                                        >
                                            Lihat
                                        </Link>

                                        <Link
                                            :href="route('pengumpulan-tugas.edit', item.id)"
                                            class="rounded-lg px-3 py-2 text-indigo-600 hover:bg-indigo-50"
                                        >
                                            Nilai
                                        </Link>

                                        <button
                                            class="rounded-lg px-3 py-2 text-red-600 hover:bg-red-50"
                                            @click="deleteData(item.id)"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr
                                v-if="props.pengumpulanTugas.data.length === 0"
                            >
                                <td
                                    colspan="8"
                                    class="px-6 py-12 text-center text-slate-500"
                                >
                                    Belum ada pengumpulan tugas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="props.pengumpulanTugas.links.length > 3"
                    class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4 dark:border-slate-800"
                >
                    <Link
                        v-for="link in props.pengumpulanTugas.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="rounded-lg px-3 py-2 text-sm"
                        :class="
                            link.active
                                ? 'bg-indigo-600 text-white'
                                : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'
                        "
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </div>
</template>