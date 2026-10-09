<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    absensi: {
        type: Object,
        required: true,
    },
})

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString(
        'id-ID',
        {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }
    )
}

const formatJam = (jam) => {
    if (!jam) return '-'

    return String(jam).substring(0, 5)
}

const formatNumber = (value, digits = 7) => {
    if (
        value === null ||
        value === undefined ||
        value === ''
    ) {
        return '-'
    }

    return Number(value).toFixed(digits)
}

const formatMeter = (value) => {
    if (
        value === null ||
        value === undefined ||
        value === ''
    ) {
        return '-'
    }

    return `${Number(value).toFixed(2)} meter`
}

const formatScore = (value) => {
    if (
        value === null ||
        value === undefined ||
        value === ''
    ) {
        return '-'
    }

    return `${Number(value).toFixed(2)}%`
}

const statusClass = (status) => {
    const classes = {
        hadir: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
        terlambat:
            'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400',
        izin: 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400',
        sakit: 'bg-purple-50 text-purple-700 dark:bg-purple-950/30 dark:text-purple-400',
        dinas: 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400',
        alpha: 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400',
    }

    return (
        classes[status] ??
        'bg-slate-100 text-slate-700'
    )
}

const statusLabel = (status) => {
    const labels = {
        hadir: 'Hadir',
        terlambat: 'Terlambat',
        izin: 'Izin',
        sakit: 'Sakit',
        dinas: 'Dinas',
        alpha: 'Alpha',
    }

    return labels[status] ?? status ?? '-'
}
</script>

<template>
    <Head title="Detail Absensi Staff" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-slate-800 dark:text-white"
                >
                    Detail Absensi Staff
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Informasi lengkap absensi staff.
                </p>
            </div>

            <Link
                :href="route('absensi-staff.index')"
                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
            >
                ← Kembali
            </Link>
        </div>

        <!-- STAFF -->
        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="p-6">

                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                        >
                            Staff
                        </p>

                        <h2
                            class="mt-1 text-xl font-bold text-slate-800 dark:text-white"
                        >
                            {{ absensi.user?.name ?? '-' }}
                        </h2>

                        <p
                            class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                        >
                            @{{ absensi.user?.username ?? '-' }}
                        </p>
                    </div>

                    <span
                        class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-bold"
                        :class="statusClass(absensi.status)"
                    >
                        {{ statusLabel(absensi.status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- WAKTU -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            <!-- MASUK -->
            <div
                class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <div class="border-b border-slate-200 p-5 dark:border-slate-700">
                    <h2
                        class="font-bold text-slate-800 dark:text-white"
                    >
                        🟢 Absensi Masuk
                    </h2>
                </div>

                <div class="space-y-4 p-5">

                    <div>
                        <p class="text-xs text-slate-500">
                            Waktu Masuk
                        </p>

                        <p
                            class="mt-1 text-lg font-bold text-slate-800 dark:text-white"
                        >
                            {{ formatJam(absensi.waktu_masuk) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">
                            Face Score
                        </p>

                        <p
                            class="mt-1 font-semibold text-slate-800 dark:text-white"
                        >
                            {{ formatScore(absensi.face_score_masuk) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">
                            Latitude
                        </p>

                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                            {{ formatNumber(absensi.latitude_masuk) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">
                            Longitude
                        </p>

                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                            {{ formatNumber(absensi.longitude_masuk) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">
                            Jarak dari lokasi
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            {{ formatMeter(absensi.jarak_masuk) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- PULANG -->
            <div
                class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <div class="border-b border-slate-200 p-5 dark:border-slate-700">
                    <h2
                        class="font-bold text-slate-800 dark:text-white"
                    >
                        🔴 Absensi Pulang
                    </h2>
                </div>

                <div class="space-y-4 p-5">

                    <div>
                        <p class="text-xs text-slate-500">
                            Waktu Pulang
                        </p>

                        <p
                            class="mt-1 text-lg font-bold text-slate-800 dark:text-white"
                        >
                            {{ formatJam(absensi.waktu_keluar) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">
                            Face Score
                        </p>

                        <p
                            class="mt-1 font-semibold text-slate-800 dark:text-white"
                        >
                            {{ formatScore(absensi.face_score_keluar) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">
                            Latitude
                        </p>

                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                            {{ formatNumber(absensi.latitude_keluar) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">
                            Longitude
                        </p>

                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                            {{ formatNumber(absensi.longitude_keluar) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">
                            Jarak dari lokasi
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            {{ formatMeter(absensi.jarak_keluar) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAIL LOKASI -->
        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="border-b border-slate-200 p-5 dark:border-slate-700">
                <h2
                    class="font-bold text-slate-800 dark:text-white"
                >
                    📍 Informasi Lokasi Absensi
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-3">

                <div>
                    <p class="text-xs text-slate-500">
                        Lokasi
                    </p>

                    <p
                        class="mt-1 font-semibold text-slate-800 dark:text-white"
                    >
                        {{ absensi.lokasi_absensi?.nama ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Radius yang diperbolehkan
                    </p>

                    <p
                        class="mt-1 font-semibold text-slate-800 dark:text-white"
                    >
                        {{ absensi.lokasi_absensi?.radius ?? '-' }}
                        meter
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Metode
                    </p>

                    <p
                        class="mt-1 font-semibold capitalize text-slate-800 dark:text-white"
                    >
                        {{ absensi.metode ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Tanggal
                    </p>

                    <p
                        class="mt-1 font-semibold text-slate-800 dark:text-white"
                    >
                        {{ formatTanggal(absensi.tanggal) }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500">
                        Keterangan
                    </p>

                    <p
                        class="mt-1 text-sm text-slate-700 dark:text-slate-300"
                    >
                        {{ absensi.keterangan || '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- KOORDINAT LOKASI RESMI -->
        <div
            v-if="absensi.lokasi_absensi"
            class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="border-b border-slate-200 p-5 dark:border-slate-700">
                <h2
                    class="font-bold text-slate-800 dark:text-white"
                >
                    Koordinat Lokasi Resmi
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-3">

                <div>
                    <p class="text-xs text-slate-500">
                        Latitude
                    </p>

                    <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                        {{ formatNumber(absensi.lokasi_absensi.latitude) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Longitude
                    </p>

                    <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                        {{ formatNumber(absensi.lokasi_absensi.longitude) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Radius
                    </p>

                    <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                        {{ absensi.lokasi_absensi.radius }} meter
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>