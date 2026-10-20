<script setup>
import { computed, reactive, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
    ClipboardList,
    Search,
    Eye,
    Pencil,
    Trash2,
    RotateCcw,
    FileText,
    Clock3,
    CheckCircle2,
    AlertCircle,
} from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    pengumpulanTugas: { type: Object, required: true },
    tugas: { type: Array, default: () => [] },
    pesertas: { type: Array, default: () => [] },
    filters: {
        type: Object,
        default: () => ({
            tugas_id: '',
            peserta_id: '',
            status: '',
        }),
    },
})

const filters = reactive({
    tugas_id: props.filters.tugas_id ?? '',
    peserta_id: props.filters.peserta_id ?? '',
    status: props.filters.status ?? '',
})

const hasFilters = computed(() =>
    Boolean(filters.tugas_id || filters.peserta_id || filters.status)
)

const statusOptions = [
    { value: 'belum_dikumpulkan', label: 'Belum dikumpulkan' },
    { value: 'dikumpulkan', label: 'Dikumpulkan' },
    { value: 'dinilai', label: 'Sudah dinilai' },
    { value: 'terlambat', label: 'Terlambat' },
]

function applyFilters() {
    router.get(route('pengumpulan-tugas.index'), filters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

function resetFilters() {
    filters.tugas_id = ''
    filters.peserta_id = ''
    filters.status = ''
    applyFilters()
}

function deleteSubmission(item) {
    if (!confirm(`Hapus pengumpulan tugas dari ${item.peserta?.nama ?? 'peserta ini'}?`)) {
        return
    }

    router.delete(route('pengumpulan-tugas.destroy', item.id), {
        preserveScroll: true,
    })
}

function formatDate(value) {
    if (!value) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value))
}

function statusClass(status) {
    const classes = {
        belum_dikumpulkan: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        dikumpulkan: 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
        dinilai: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
        terlambat: 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300',
    }

    return classes[status] ?? 'bg-slate-100 text-slate-600'
}

function statusLabel(status) {
    return statusOptions.find(item => item.value === status)?.label ?? status ?? '-'
}

watch(
    () => props.filters,
    value => {
        filters.tugas_id = value?.tugas_id ?? ''
        filters.peserta_id = value?.peserta_id ?? ''
        filters.status = value?.status ?? ''
    },
    { deep: true },
)
</script>

<template>
    <Head title="Pengumpulan Tugas" />

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <ClipboardList class="size-4" />
                    <span>LMS BLKK / Akademik</span>
                </div>

                <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                    Pengumpulan Tugas
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pantau pengumpulan dan hasil penilaian tugas peserta.
                </p>
            </div>

            <Link
                :href="route('pengumpulan-tugas.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
            >
                <FileText class="size-4" />
                Tambah Pengumpulan
            </Link>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Total Data</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">
                    {{ pengumpulanTugas.total ?? 0 }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Sudah Dinilai</p>
                <p class="mt-2 text-2xl font-bold text-emerald-600">
                    {{ pengumpulanTugas.data.filter(item => item.status === 'dinilai').length }}
                </p>
                <p class="mt-1 text-xs text-slate-400">Pada halaman ini</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum Dinilai</p>
                <p class="mt-2 text-2xl font-bold text-amber-600">
                    {{ pengumpulanTugas.data.filter(item => item.status === 'dikumpulkan').length }}
                </p>
                <p class="mt-1 text-xs text-slate-400">Pada halaman ini</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Terlambat</p>
                <p class="mt-2 text-2xl font-bold text-red-600">
                    {{ pengumpulanTugas.data.filter(item => item.status === 'terlambat').length }}
                </p>
                <p class="mt-1 text-xs text-slate-400">Pada halaman ini</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 p-5 dark:border-slate-800">
                <div class="mb-4">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Filter Data</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Pilih tugas, peserta, atau status untuk menyaring data.
                    </p>
                </div>

                <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-4" @submit.prevent="applyFilters">
                    <select
                        v-model="filters.tugas_id"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                        <option value="">Semua tugas</option>
                        <option v-for="item in tugas" :key="item.id" :value="String(item.id)">
                            {{ item.judul }}
                        </option>
                    </select>

                    <select
                        v-model="filters.peserta_id"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                        <option value="">Semua peserta</option>
                        <option v-for="item in pesertas" :key="item.id" :value="String(item.id)">
                            {{ item.nama }}
                        </option>
                    </select>

                    <select
                        v-model="filters.status"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                        <option value="">Semua status</option>
                        <option v-for="item in statusOptions" :key="item.value" :value="item.value">
                            {{ item.label }}
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            <Search class="size-4" />
                            Filter
                        </button>

                        <button
                            v-if="hasFilters"
                            type="button"
                            title="Reset filter"
                            class="rounded-xl border border-slate-200 px-3 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            @click="resetFilters"
                        >
                            <RotateCcw class="size-4" />
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950 dark:text-slate-400">
                        <tr>
                            <th class="px-5 py-4">Peserta / Tugas</th>
                            <th class="px-5 py-4">Waktu Pengumpulan</th>
                            <th class="px-5 py-4">Nilai</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="item in pengumpulanTugas.data" :key="item.id" class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900 dark:text-white">
                                    {{ item.peserta?.nama ?? 'Peserta tidak ditemukan' }}
                                </p>
                                <p class="mt-1 text-slate-500 dark:text-slate-400">
                                    {{ item.tugas?.judul ?? 'Tugas tidak ditemukan' }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                <div class="flex items-center gap-2">
                                    <Clock3 class="size-4 shrink-0 text-slate-400" />
                                    {{ formatDate(item.dikumpulkan_at) }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-semibold text-slate-900 dark:text-white">
                                    {{ item.nilai !== null ? `${item.nilai}/100` : '-' }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span :class="['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium', statusClass(item.status)]">
                                    <CheckCircle2 v-if="item.status === 'dinilai'" class="size-3.5" />
                                    <AlertCircle v-else-if="item.status === 'terlambat'" class="size-3.5" />
                                    {{ statusLabel(item.status) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-1">
                                    <Link
                                        :href="route('pengumpulan-tugas.show', item.id)"
                                        title="Detail"
                                        class="rounded-lg p-2 text-slate-500 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-950"
                                    >
                                        <Eye class="size-4" />
                                    </Link>

                                    <Link
                                        :href="route('pengumpulan-tugas.edit', item.id)"
                                        title="Nilai tugas"
                                        class="rounded-lg p-2 text-slate-500 hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-950"
                                    >
                                        <Pencil class="size-4" />
                                    </Link>

                                    <button
                                        type="button"
                                        title="Hapus"
                                        class="rounded-lg p-2 text-slate-500 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950"
                                        @click="deleteSubmission(item)"
                                    >
                                        <Trash2 class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!pengumpulanTugas.data.length">
                            <td colspan="5" class="px-5 py-14 text-center">
                                <ClipboardList class="mx-auto size-10 text-slate-300" />
                                <p class="mt-3 font-medium text-slate-700 dark:text-slate-200">
                                    Belum ada pengumpulan tugas
                                </p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Data akan tampil setelah ada pengumpulan.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col justify-between gap-4 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center dark:border-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Menampilkan {{ pengumpulanTugas.from ?? 0 }}–{{ pengumpulanTugas.to ?? 0 }}
                    dari {{ pengumpulanTugas.total ?? 0 }} data
                </p>

                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, index) in pengumpulanTugas.links" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            class="rounded-lg border px-3 py-2 text-sm"
                            :class="link.active
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="cursor-not-allowed rounded-lg border border-slate-100 px-3 py-2 text-sm text-slate-300 dark:border-slate-800 dark:text-slate-600"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </section>
    </div>
</template>
