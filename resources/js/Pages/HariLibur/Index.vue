<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    hariLibur: {
        type: Object,
        default: () => ({
            data: [],
        }),
    },
})

const formatTanggal = (tanggal) => {
    if (!tanggal) {
        return '-'
    }

    const date = new Date(tanggal)

    if (isNaN(date.getTime())) {
        return '-'
    }

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

const formatHari = (tanggal) => {
    if (!tanggal) {
        return '-'
    }

    const date = new Date(tanggal)

    if (isNaN(date.getTime())) {
        return '-'
    }

    return date.toLocaleDateString('id-ID', {
        weekday: 'long',
    })
}

const hapus = (item) => {
    if (
        confirm(
            `Yakin ingin menghapus hari libur "${item.nama}" pada ${formatTanggal(item.tanggal)}?`
        )
    ) {
        router.delete(route('hari-libur.destroy', item.id))
    }
}
</script>

<template>
    <Head title="Hari Libur" />

    <div class="space-y-6">
        <!-- HEADER -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-slate-800 dark:text-white"
                >
                    Hari Libur
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Atur tanggal libur yang membuat absensi otomatis tidak
                    tersedia.
                </p>
            </div>

            <Link
                :href="route('hari-libur.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
            >
                <span class="text-lg">+</span>
                Tambah Hari Libur
            </Link>
        </div>

        <!-- INFO -->
        <div
            class="rounded-2xl border border-amber-100 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-950/20"
        >
            <div class="flex gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-lg dark:bg-amber-900/40"
                >
                    📅
                </div>

                <div>
                    <h3
                        class="text-sm font-semibold text-amber-900 dark:text-amber-300"
                    >
                        Pengaturan hari libur
                    </h3>

                    <p
                        class="mt-1 text-sm text-amber-700 dark:text-amber-400"
                    >
                        Jika tanggal aktif terdaftar sebagai hari libur,
                        sistem akan menonaktifkan absensi staff pada tanggal
                        tersebut meskipun terdapat jadwal kerja.
                    </p>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead
                        class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/70"
                    >
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Tanggal
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Nama Hari Libur
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Keterangan
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Status
                            </th>

                            <th
                                class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        class="divide-y divide-slate-200 dark:divide-slate-700"
                    >
                        <tr
                            v-for="item in props.hariLibur.data"
                            :key="item.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        >
                            <!-- TANGGAL -->
                            <td class="px-6 py-4">
                                <div
                                    class="font-semibold text-slate-800 dark:text-white"
                                >
                                    {{ formatTanggal(item.tanggal) }}
                                </div>

                                <div
                                    class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ formatHari(item.tanggal) }}
                                </div>
                            </td>

                            <!-- NAMA -->
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm font-semibold text-slate-700 dark:text-slate-300"
                                >
                                    {{ item.nama }}
                                </span>
                            </td>

                            <!-- KETERANGAN -->
                            <td class="max-w-xs px-6 py-4">
                                <p
                                    class="truncate text-sm text-slate-600 dark:text-slate-400"
                                >
                                    {{ item.keterangan || '-' }}
                                </p>
                            </td>

                            <!-- STATUS -->
                            <td class="px-6 py-4">
                                <span
                                    v-if="item.status"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                    ></span>
                                    Aktif
                                </span>

                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-400"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-slate-400"
                                    ></span>
                                    Nonaktif
                                </span>
                            </td>

                            <!-- AKSI -->
                            <td class="px-6 py-4">
                                <div
                                    class="flex justify-center gap-2"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'hari-libur.show',
                                                item.id
                                            )
                                        "
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-indigo-700 dark:hover:text-indigo-400"
                                    >
                                        Lihat
                                    </Link>

                                    <Link
                                        :href="
                                            route(
                                                'hari-libur.edit',
                                                item.id
                                            )
                                        "
                                        class="rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        @click="hapus(item)"
                                        class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- EMPTY -->
                        <tr
                            v-if="
                                !props.hariLibur.data ||
                                props.hariLibur.data.length === 0
                            "
                        >
                            <td
                                colspan="5"
                                class="px-6 py-16 text-center"
                            >
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl dark:bg-slate-800"
                                >
                                    📅
                                </div>

                                <h3
                                    class="mt-4 font-semibold text-slate-800 dark:text-white"
                                >
                                    Belum ada hari libur
                                </h3>

                                <p
                                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Belum ada tanggal yang ditetapkan sebagai
                                    hari libur.
                                </p>

                                <Link
                                    :href="route('hari-libur.create')"
                                    class="mt-4 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                                >
                                    Tambah Hari Libur
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div
                v-if="
                    props.hariLibur.links &&
                    props.hariLibur.links.length > 3
                "
                class="flex flex-wrap gap-1 border-t border-slate-200 px-6 py-4 dark:border-slate-700"
            >
                <template
                    v-for="(link, index) in props.hariLibur.links"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        preserve-scroll
                        class="rounded-lg px-3 py-2 text-sm transition"
                        :class="
                            link.active
                                ? 'bg-indigo-600 font-semibold text-white'
                                : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                        "
                    >
                        <span v-html="link.label"></span>
                    </Link>

                    <span
                        v-else
                        class="rounded-lg px-3 py-2 text-sm text-slate-400"
                        v-html="link.label"
                    ></span>
                </template>
            </div>
        </div>
    </div>
</template>