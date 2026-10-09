<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    jadwal: {
        type: Object,
        required: true,
    },
})

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

const formatJam = (jam) => {
    if (!jam) return '-'

    return String(jam).substring(0, 5)
}

const statusLabel = (status) => {
    const labels = {
        belum_dimulai: 'Belum Dimulai',
        berlangsung: 'Berlangsung',
        selesai: 'Selesai',
        dibatalkan: 'Dibatalkan',
    }

    return labels[status] ?? status
}

const statusClass = (status) => {
    const classes = {
        belum_dimulai:
            'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400',

        berlangsung:
            'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-400/10 dark:text-indigo-400',

        selesai:
            'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-400',

        dibatalkan:
            'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-400/10 dark:text-red-400',
    }

    return classes[status] ?? 'bg-slate-50 text-slate-600 ring-slate-600/20'
}

const absensiStatusLabel = (status) => {
    const labels = {
        hadir: 'Hadir',
        terlambat: 'Terlambat',
        izin: 'Izin',
        sakit: 'Sakit',
        alpa: 'Alpa',
    }

    return labels[status] ?? status
}

const absensiStatusClass = (status) => {
    const classes = {
        hadir:
            'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-400',

        terlambat:
            'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-400',

        izin:
            'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-400',

        sakit:
            'bg-sky-50 text-sky-700 dark:bg-sky-400/10 dark:text-sky-400',

        alpa:
            'bg-red-50 text-red-700 dark:bg-red-400/10 dark:text-red-400',
    }

    return (
        classes[status] ??
        'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'
    )
}

const formatDateTime = (datetime) => {
    if (!datetime) return '-'

    return new Date(datetime).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>

<template>
    <Head title="Detail Jadwal" />

<div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">

        <!-- HEADER -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-center gap-3">
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm"
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
                            stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                </div>

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Detail Jadwal
                    </h1>

                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Informasi lengkap jadwal kegiatan
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">

                <Link
                    :href="route('jadwals.edit', jadwal.id)"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
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
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 8.5-8.5z"
                        />
                    </svg>

                    Edit
                </Link>

                <Link
                    :href="route('jadwals.index')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
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
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>

                    Kembali
                </Link>

            </div>
        </div>

        <!-- MAIN INFO -->
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            <!-- PROGRAM -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:col-span-2"
            >
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                            Program Pelatihan
                        </p>

                        <h2 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">
                            {{ jadwal.program_pelatihan?.nama ?? '-' }}
                        </h2>
                    </div>

                    <span
                        class="inline-flex shrink-0 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset"
                        :class="statusClass(jadwal.status)"
                    >
                        {{ statusLabel(jadwal.status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <!-- PELATIHAN -->
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/70">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                            Pelatihan
                        </p>

                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                            {{ jadwal.program_pelatihan?.pelatihan?.nama ?? '-' }}
                        </p>
                    </div>

                    <!-- TAHAP -->
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/70">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                            Tahap
                        </p>

                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                            {{ jadwal.program_pelatihan?.tahap?.nama ?? '-' }}
                        </p>
                    </div>

                    <!-- KELAS -->
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/70 sm:col-span-2">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                                    Kelas
                                </p>

                                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                                    Kelas yang mengikuti jadwal ini
                                </p>
                            </div>

                            <span
                                class="rounded-lg bg-indigo-100 px-2.5 py-1 text-xs font-bold text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-400"
                            >
                                {{ jadwal.kelas?.length ?? 0 }} Kelas
                            </span>
                        </div>

                        <div
                            v-if="jadwal.kelas?.length"
                            class="mt-4 flex flex-wrap gap-2"
                        >
                            <div
                                v-for="kelas in jadwal.kelas"
                                :key="kelas.id"
                                class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 dark:border-slate-700 dark:bg-slate-900"
                            >
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ kelas.nama }}
                                </p>

                                <p
                                    v-if="kelas.durasi_hari"
                                    class="mt-0.5 text-xs text-slate-500 dark:text-slate-400"
                                >
                                    Durasi {{ kelas.durasi_hari }} hari
                                </p>
                            </div>
                        </div>

                        <p
                            v-else
                            class="mt-3 text-sm text-slate-500 dark:text-slate-400"
                        >
                            Belum ada kelas yang dipilih.
                        </p>
                    </div>

                    <!-- PERTEMUAN -->
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/70">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                            Pertemuan
                        </p>

                        <p class="mt-1 text-xl font-bold text-indigo-600 dark:text-indigo-400">
                            Pertemuan {{ jadwal.pertemuan }}
                        </p>
                    </div>

                    <!-- JUMLAH KELAS -->
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/70">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                            Jumlah Kelas
                        </p>

                        <p class="mt-1 text-xl font-bold text-slate-900 dark:text-white">
                            {{ jadwal.kelas?.length ?? 0 }}
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Kelas
                            </span>
                        </p>
                    </div>

                </div>
            </div>

            <!-- WAKTU -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-400/10">
                    <svg
                        class="h-6 w-6 text-indigo-600 dark:text-indigo-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>

                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Waktu Pelaksanaan
                </p>

                <p class="mt-3 text-lg font-bold text-slate-900 dark:text-white">
                    {{ formatTanggal(jadwal.tanggal) }}
                </p>

                <div class="mt-4 flex items-center gap-2">
                    <span class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        {{ formatJam(jadwal.jam_mulai) }}
                    </span>

                    <span class="text-slate-400">
                        sampai
                    </span>

                    <span class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        {{ formatJam(jadwal.jam_selesai) }}
                    </span>
                </div>
            </div>

        </div>

        <!-- KEGIATAN -->
        <div
            class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    Informasi Kegiatan
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Detail kegiatan pada jadwal ini.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Kegiatan
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">
                        {{ jadwal.kegiatan || 'Tidak ada nama kegiatan' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Keterangan
                    </p>

                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">
                        {{ jadwal.keterangan || 'Tidak ada keterangan.' }}
                    </p>
                </div>

            </div>
        </div>

        <!-- ABSENSI -->
        <div
            class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="flex flex-col gap-3 border-b border-slate-200 p-6 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">
                        Data Absensi
                    </h2>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Peserta yang tercatat pada jadwal ini.
                    </p>
                </div>

                <div class="rounded-xl bg-indigo-50 px-4 py-2 dark:bg-indigo-400/10">
                    <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                        {{ jadwal.absensis?.length ?? 0 }} Peserta
                    </span>
                </div>
            </div>

            <div
                v-if="!jadwal.absensis || jadwal.absensis.length === 0"
                class="px-6 py-14 text-center"
            >
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                    <svg
                        class="h-7 w-7 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                </div>

                <p class="mt-4 font-semibold text-slate-900 dark:text-white">
                    Belum ada data absensi
                </p>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Belum ada peserta yang tercatat hadir pada jadwal ini.
                </p>
            </div>

            <div
                v-else
                class="overflow-x-auto"
            >
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Peserta
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Waktu Masuk
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Waktu Keluar
                            </th>

                            <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Status
                            </th>

                            <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Metode
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr
                            v-for="absensi in jadwal.absensis"
                            :key="absensi.id"
                            class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40"
                        >
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900 dark:text-white">
                                    {{ absensi.pendaftaran?.peserta?.nama ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    ID Pendaftaran:
                                    {{ absensi.pendaftaran_id }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ formatDateTime(absensi.waktu_masuk) }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ formatDateTime(absensi.waktu_keluar) }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="absensiStatusClass(absensi.status)"
                                >
                                    {{ absensiStatusLabel(absensi.status) }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ absensi.metode === 'face' ? 'Face Recognition' : 'Manual' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</template>
