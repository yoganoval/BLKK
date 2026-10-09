<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pendaftaran: {
        type: Object,
        required: true,
    },
})

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

const formatDateTime = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const statusClass = (status) => {
    switch (status) {
        case 'aktif':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
        case 'selesai':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
        case 'batal':
            return 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400'
        default:
            return 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
    }
}
</script>

<template>
    <Head title="Detail Pendaftaran" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <div class="mb-3 flex items-center gap-2 text-sm text-slate-500">
                        <Link
                            :href="route('pendaftarans.index')"
                            class="hover:text-indigo-600"
                        >
                            Pendaftaran
                        </Link>

                        <span>/</span>

                        <span>Detail</span>
                    </div>

                    <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                        Detail Pendaftaran
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Informasi lengkap pendaftaran peserta
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        :href="route('pendaftarans.index')"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                    >
                        Kembali
                    </Link>

                    <Link
                        :href="route('pendaftarans.edit', pendaftaran.id)"
                        class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Edit
                    </Link>
                </div>
            </div>

            <!-- PESERTA -->
            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 p-6 dark:border-slate-800">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-xl font-bold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            {{ pendaftaran.peserta?.nama?.charAt(0)?.toUpperCase() || '?' }}
                        </div>

                        <div class="flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Peserta
                            </p>

                            <h2 class="mt-1 text-2xl font-bold text-slate-800 dark:text-white">
                                {{ pendaftaran.peserta?.nama || '-' }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                ID Peserta #{{ pendaftaran.peserta?.id || '-' }}
                            </p>
                        </div>

                        <span
                            class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold"
                            :class="statusClass(pendaftaran.status)"
                        >
                            {{ pendaftaran.status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2 lg:grid-cols-4">

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Kamar
                        </p>

                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ pendaftaran.peserta?.kamar || '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Lembaga
                        </p>

                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ pendaftaran.peserta?.lembaga || '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Tanggal Daftar
                        </p>

                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ formatTanggal(pendaftaran.tanggal_daftar) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            ID Pendaftaran
                        </p>

                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            #{{ pendaftaran.id }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- PROGRAM -->
            <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <div class="mb-5 flex items-center gap-3">
                        <div class="rounded-xl bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M12 14l9-5-9-5-9 5 9 5zM5 12v5l7 4 7-4v-5"
                                />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white">
                                Program Pelatihan
                            </h3>

                            <p class="text-xs text-slate-500">
                                Program yang diikuti peserta
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">

                        <div>
                            <p class="text-xs text-slate-400">
                                Nama Program
                            </p>

                            <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                                {{ pendaftaran.program_pelatihan?.nama || '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">
                                Pelatihan
                            </p>

                            <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                                {{ pendaftaran.program_pelatihan?.pelatihan?.nama || '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">
                                Tahap
                            </p>

                            <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                                Tahap {{ pendaftaran.program_pelatihan?.tahap?.urutan || '-' }}
                                -
                                {{ pendaftaran.program_pelatihan?.tahap?.nama || '-' }}
                            </p>
                        </div>

                    </div>
                </div>

                <!-- KELAS -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                    <div class="mb-5 flex items-center gap-3">
                        <div class="rounded-xl bg-purple-50 p-3 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white">
                                Kelas
                            </h3>

                            <p class="text-xs text-slate-500">
                                Kelas yang dipilih peserta
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">

                        <div>
                            <p class="text-xs text-slate-400">
                                Nama Kelas
                            </p>

                            <p class="mt-1 text-lg font-bold text-slate-800 dark:text-white">
                                {{ pendaftaran.kelas?.nama || '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">
                                Durasi
                            </p>

                            <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                                {{ pendaftaran.kelas?.durasi_hari || 0 }} Hari
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">
                                Keterangan
                            </p>

                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                                {{ pendaftaran.kelas?.keterangan || 'Tidak ada keterangan.' }}
                            </p>
                        </div>

                    </div>
                </div>

            </div>

            <!-- PEMBAYARAN -->
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                    <h3 class="font-bold text-slate-800 dark:text-white">
                        Riwayat Pembayaran
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Data pembayaran yang terkait dengan pendaftaran ini
                    </p>
                </div>

                <div
                    v-if="!pendaftaran.pembayarans?.length"
                    class="px-6 py-10 text-center text-sm text-slate-500"
                >
                    Belum ada data pembayaran.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800">
                                <th class="px-6 py-3 text-xs font-semibold uppercase text-slate-500">
                                    Tanggal
                                </th>

                                <th class="px-6 py-3 text-xs font-semibold uppercase text-slate-500">
                                    Jumlah
                                </th>

                                <th class="px-6 py-3 text-xs font-semibold uppercase text-slate-500">
                                    Metode
                                </th>

                                <th class="px-6 py-3 text-xs font-semibold uppercase text-slate-500">
                                    Status
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="payment in pendaftaran.pembayarans"
                                :key="payment.id"
                            >
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ formatTanggal(payment.tanggal_bayar) }}
                                </td>

                                <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-white">
                                    Rp {{ Number(payment.jumlah || 0).toLocaleString('id-ID') }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ payment.metode || '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        {{ payment.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABSENSI -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                    <h3 class="font-bold text-slate-800 dark:text-white">
                        Riwayat Absensi
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Riwayat kehadiran peserta selama mengikuti pelatihan
                    </p>
                </div>

                <div
                    v-if="!pendaftaran.absensis?.length"
                    class="px-6 py-10 text-center text-sm text-slate-500"
                >
                    Belum ada data absensi.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800">
                                <th class="px-6 py-3 text-xs font-semibold uppercase text-slate-500">
                                    Pertemuan
                                </th>

                                <th class="px-6 py-3 text-xs font-semibold uppercase text-slate-500">
                                    Tanggal
                                </th>

                                <th class="px-6 py-3 text-xs font-semibold uppercase text-slate-500">
                                    Masuk
                                </th>

                                <th class="px-6 py-3 text-xs font-semibold uppercase text-slate-500">
                                    Status
                                </th>

                                <th class="px-6 py-3 text-xs font-semibold uppercase text-slate-500">
                                    Metode
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="absensi in pendaftaran.absensis"
                                :key="absensi.id"
                            >
                                <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Pertemuan {{ absensi.jadwal?.pertemuan || '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ formatTanggal(absensi.jadwal?.tanggal) }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ formatDateTime(absensi.waktu_masuk) }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        {{ absensi.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ absensi.metode || '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</template>