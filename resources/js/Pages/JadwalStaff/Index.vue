<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    jadwals: {
        type: Array,
        default: () => [],
    },
})

const hariLabel = {
    senin: 'Senin',
    selasa: 'Selasa',
    rabu: 'Rabu',
    kamis: 'Kamis',
    jumat: 'Jumat',
    sabtu: 'Sabtu',
    minggu: 'Minggu',
}

const formatJam = (jam) => {
    if (!jam) return '-'
    return jam.substring(0, 5)
}

const hapus = (jadwal) => {
    if (
        confirm(
            `Yakin ingin menghapus jadwal "${jadwal.nama}" pada hari ${hariLabel[jadwal.hari]}?`
        )
    ) {
        router.delete(route('jadwal-staff.destroy', jadwal.id))
    }
}
</script>

<template>
    <Head title="Jadwal Staff" />

    <div class="space-y-6">
        <!-- HEADER -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-slate-800 dark:text-white"
                >
                    Jadwal Staff
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Atur jadwal kerja dan waktu absensi staff berdasarkan hari.
                </p>
            </div>

            <Link
                :href="route('jadwal-staff.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
            >
                <span class="text-lg">+</span>
                Tambah Jadwal
            </Link>
        </div>

        <!-- INFO -->
        <div
            class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 dark:border-indigo-900/50 dark:bg-indigo-950/30"
        >
            <div class="flex gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400"
                >
                    ℹ
                </div>

                <div>
                    <h3
                        class="text-sm font-semibold text-indigo-900 dark:text-indigo-300"
                    >
                        Jadwal mingguan
                    </h3>

                    <p
                        class="mt-1 text-sm text-indigo-700 dark:text-indigo-400"
                    >
                        Jadwal ini berlaku otomatis setiap minggu. Tidak perlu
                        membuat jadwal untuk setiap tanggal.
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
                                Jadwal
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Hari
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Jam Kerja
                            </th>

                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Toleransi
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
                            v-for="jadwal in props.jadwals"
                            :key="jadwal.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        >
                            <!-- NAMA -->
                            <td class="px-6 py-4">
                                <div
                                    class="font-semibold text-slate-800 dark:text-white"
                                >
                                    {{ jadwal.nama }}
                                </div>

                                <div
                                    v-if="jadwal.keterangan"
                                    class="mt-1 max-w-xs truncate text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ jadwal.keterangan }}
                                </div>
                            </td>

                            <!-- HARI -->
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                >
                                    {{ hariLabel[jadwal.hari] }}
                                </span>
                            </td>

                            <!-- JAM -->
                            <td class="px-6 py-4">
                                <div
                                    class="font-semibold text-slate-800 dark:text-white"
                                >
                                    {{ formatJam(jadwal.jam_masuk) }}
                                    -
                                    {{ formatJam(jadwal.jam_pulang) }}
                                </div>
                            </td>

                            <!-- TOLERANSI -->
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm text-slate-600 dark:text-slate-300"
                                >
                                    {{ jadwal.toleransi_menit }} menit
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td class="px-6 py-4">
                                <span
                                    v-if="jadwal.status"
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
                                                'jadwal-staff.show',
                                                jadwal.id
                                            )
                                        "
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-indigo-700 dark:hover:text-indigo-400"
                                    >
                                        Lihat
                                    </Link>

                                    <Link
                                        :href="
                                            route(
                                                'jadwal-staff.edit',
                                                jadwal.id
                                            )
                                        "
                                        class="rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 dark:hover:bg-indigo-900/50"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        @click="hapus(jadwal)"
                                        class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- EMPTY -->
                        <tr v-if="props.jadwals.length === 0">
                            <td
                                colspan="6"
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
                                    Belum ada jadwal
                                </h3>

                                <p
                                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Silakan tambahkan jadwal kerja staff.
                                </p>

                                <Link
                                    :href="route('jadwal-staff.create')"
                                    class="mt-4 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                                >
                                    Tambah Jadwal
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>