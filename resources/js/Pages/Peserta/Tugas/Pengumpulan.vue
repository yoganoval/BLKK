<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PesertaLayout from '@/Layouts/PesertaLayout.vue'

defineOptions({
    layout: PesertaLayout,
})

const props = defineProps({
    pengumpulan: {
        type: Object,
        required: true,
    },
})

const daftarPengumpulan = computed(() => props.pengumpulan.data ?? [])

const totalPengumpulan = computed(
    () => props.pengumpulan.total ?? daftarPengumpulan.value.length
)

const totalDinilai = computed(
    () => daftarPengumpulan.value.filter(item => item.status === 'dinilai').length
)

const totalBelumDinilai = computed(
    () => daftarPengumpulan.value.filter(item => item.status !== 'dinilai').length
)

function formatTanggal(tanggal) {
    if (!tanggal) return '-'

    const date = new Date(tanggal)

    if (Number.isNaN(date.getTime())) return tanggal

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

function namaTugas(item) {
    return item.tugas?.judul
        || item.tugas?.nama_tugas
        || item.tugas?.nama
        || '-'
}

function fileUrl(path) {
    return `/storage/${path}`
}

function labelStatus(status) {
    const labels = {
        dinilai: 'Sudah dinilai',
        terkirim: 'Terkirim',
        dikumpulkan: 'Terkirim',
        terlambat: 'Terlambat',
        revisi: 'Perlu revisi',
    }

    return labels[status] ?? status ?? 'Terkirim'
}
</script>

<template>
    <Head title="Riwayat Pengumpulan Tugas" />

    <div class="min-h-screen bg-gray-50 px-4 py-6 text-gray-900
                dark:bg-gray-950 dark:text-gray-100 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-7xl space-y-6">

            <!-- Navigasi -->
            <Link
                :href="route('peserta.dashboard')"
                class="inline-flex items-center gap-2 text-sm font-medium
                       text-gray-500 transition hover:text-blue-600
                       dark:text-gray-400 dark:hover:text-blue-400"
            >
                <ArrowLeft class="h-4 w-4" />
                Kembali ke Dashboard
            </Link>

            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row
                        sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-blue-100 p-3
                                    text-blue-700 dark:bg-blue-900/40
                                    dark:text-blue-300">
                            <ClipboardCheck class="h-6 w-6" />
                        </div>

                        <div>
                            <h1 class="text-2xl font-bold tracking-tight
                                       sm:text-3xl">
                                Riwayat Pengumpulan
                            </h1>
                            <p class="mt-1 text-sm text-gray-500
                                      dark:text-gray-400">
                                Pantau tugas yang telah kamu kirim.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 rounded-xl border
                            border-gray-200 bg-white px-4 py-3
                            shadow-sm dark:border-gray-800
                            dark:bg-gray-900">
                    <div class="rounded-lg bg-emerald-100 p-2
                                text-emerald-700 dark:bg-emerald-900/40
                                dark:text-emerald-300">
                        <FileText class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Total pengumpulan
                        </p>
                        <p class="text-xl font-bold">
                            {{ pengumpulan.total ?? pengumpulan.data.length }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Konten -->
            <section class="overflow-hidden rounded-2xl border
                            border-gray-200 bg-white shadow-sm
                            dark:border-gray-800 dark:bg-gray-900">

                <div class="border-b border-gray-200 px-5 py-4
                            dark:border-gray-800 sm:px-6">
                    <h2 class="font-semibold">Daftar Pengumpulan Tugas</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Berikut adalah riwayat tugas yang sudah dikumpulkan.
                    </p>
                </div>

                <!-- Jika kosong -->
                <div
                    v-if="!pengumpulan.data?.length"
                    class="px-5 py-16 text-center sm:px-6"
                >
                    <div class="mx-auto flex h-16 w-16 items-center
                                justify-center rounded-2xl bg-gray-100
                                text-gray-400 dark:bg-gray-800">
                        <Inbox class="h-8 w-8" />
                    </div>

                    <h3 class="mt-4 text-lg font-semibold">
                        Belum Ada Pengumpulan
                    </h3>

                    <p class="mx-auto mt-2 max-w-sm text-sm leading-6
                              text-gray-500 dark:text-gray-400">
                        Kamu belum mengumpulkan tugas apa pun.
                        Tugas yang sudah dikirim akan muncul di halaman ini.
                    </p>

                    <Link
                        :href="route('peserta.tugas.index')"
                        class="mt-5 inline-flex items-center justify-center
                               rounded-lg bg-blue-600 px-4 py-2.5
                               text-sm font-semibold text-white
                               transition hover:bg-blue-700"
                    >
                        Lihat Daftar Tugas
                    </Link>
                </div>

                <template v-else>
                    <!-- Tampilan desktop -->
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase
                                           tracking-wider text-gray-500
                                           dark:bg-gray-800/70
                                           dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-4">No.</th>
                                    <th class="px-6 py-4">Tugas</th>
                                    <th class="px-6 py-4">Jawaban</th>
                                    <th class="px-6 py-4">Lampiran</th>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100
                                          dark:divide-gray-800">
                                <tr
                                    v-for="(item, index) in pengumpulan.data"
                                    :key="item.id"
                                    class="transition hover:bg-gray-50/80
                                           dark:hover:bg-gray-800/40"
                                >
                                    <td class="whitespace-nowrap px-6 py-4
                                               text-gray-500">
                                        {{ (pengumpulan.from ?? 1) + index }}
                                    </td>

                                    <td class="min-w-48 px-6 py-4">
                                        <p class="font-semibold">
                                            {{ namaTugas(item) }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            ID: {{ item.id }}
                                        </p>
                                    </td>

                                    <td class="max-w-xs px-6 py-4">
                                        <p class="line-clamp-2 text-gray-600
                                                  dark:text-gray-300">
                                            {{ item.jawaban || 'Tidak ada jawaban tertulis' }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <a
                                            v-if="item.file_path"
                                            :href="fileUrl(item.file_path)"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2
                                                   font-medium text-blue-600
                                                   hover:text-blue-700
                                                   dark:text-blue-400"
                                        >
                                            <FileText class="h-4 w-4" />
                                            Lihat file
                                            <ExternalLink class="h-3.5 w-3.5" />
                                        </a>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4
                                               text-gray-600 dark:text-gray-300">
                                        {{ formatTanggal(item.created_at) }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center
                                                   rounded-full px-2.5 py-1
                                                   text-xs font-semibold"
                                            :class="item.status === 'dinilai'
                                                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                                : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'"
                                        >
                                            {{ item.status === 'dinilai' ? 'Sudah dinilai' : (item.status || 'Terkirim') }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tampilan mobile -->
                    <div class="divide-y divide-gray-100 md:hidden
                                dark:divide-gray-800">
                        <article
                            v-for="(item, index) in pengumpulan.data"
                            :key="item.id"
                            class="space-y-4 p-5"
                        >
                            <div class="flex items-start gap-3">
                                <div class="rounded-xl bg-blue-100 p-3
                                            text-blue-700 dark:bg-blue-900/40
                                            dark:text-blue-300">
                                    <FileText class="h-5 w-5" />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-gray-500">
                                        Pengumpulan #{{ (pengumpulan.from ?? 1) + index }}
                                    </p>
                                    <h3 class="mt-1 font-semibold">
                                        {{ namaTugas(item) }}
                                    </h3>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-gray-500">
                                    Jawaban
                                </p>
                                <p class="mt-1 whitespace-pre-line break-words
                                          text-sm text-gray-700
                                          dark:text-gray-300">
                                    {{ item.jawaban || 'Tidak ada jawaban tertulis' }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center
                                        justify-between gap-3">
                                <div class="flex items-center gap-2 text-xs
                                            text-gray-500 dark:text-gray-400">
                                    <CalendarDays class="h-4 w-4" />
                                    {{ formatTanggal(item.created_at) }}
                                </div>

                                <span
                                    class="rounded-full px-2.5 py-1 text-xs
                                           font-semibold"
                                    :class="item.status === 'dinilai'
                                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                        : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'"
                                >
                                    {{ item.status === 'dinilai' ? 'Sudah dinilai' : (item.status || 'Terkirim') }}
                                </span>
                            </div>

                            <a
                                v-if="item.file_path"
                                :href="fileUrl(item.file_path)"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 text-sm
                                       font-semibold text-blue-600
                                       dark:text-blue-400"
                            >
                                <FileText class="h-4 w-4" />
                                Lihat lampiran
                                <ExternalLink class="h-3.5 w-3.5" />
                            </a>
                        </article>
                    </div>

                    <!-- Pagination Laravel -->
                    <div
                        v-if="pengumpulan.links?.length > 3"
                        class="flex flex-wrap items-center justify-between
                               gap-3 border-t border-gray-200 px-5 py-4
                               dark:border-gray-800 sm:px-6"
                    >
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Halaman {{ pengumpulan.current_page }}
                            dari {{ pengumpulan.last_page }}
                        </p>

                        <div class="flex flex-wrap items-center gap-1">
                            <template
                                v-for="(link, index) in pengumpulan.links"
                                :key="index"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-scroll
                                    class="flex h-9 min-w-9 items-center
                                           justify-center rounded-lg border
                                           px-3 text-sm transition"
                                    :class="link.active
                                        ? 'border-blue-600 bg-blue-600 text-white'
                                        : 'border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800'"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="flex h-9 min-w-9 cursor-not-allowed
                                           items-center justify-center
                                           rounded-lg border border-gray-100
                                           px-3 text-sm text-gray-300
                                           dark:border-gray-800 dark:text-gray-600"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </template>
            </section>
        </div>
    </div>
</template>
