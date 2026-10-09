<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import BaseCard from '@/Components/BaseCard.vue'
import BaseBadge from '@/Components/BaseBadge.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            peserta: 0,
            instruktur: 0,
            kelas: 0,
            kelulusan: '0%',
            pendaftaran_baru: 0,
            sertifikasi: 0,
        }),
    },

    presensi: {
        type: Object,
        default: () => ({
            hadir: 0,
            izin: 0,
            sakit: 0,
            alpha: 0,
            total_peserta: 0,
        }),
    },

    kelasAktif: {
        type: Array,
        default: () => [],
    },

    pendaftaranTerbaru: {
        type: Array,
        default: () => [],
    },

    kegiatanTerbaru: {
        type: Array,
        default: () => [],
    },

    statistikBulanan: {
        type: Object,
        default: () => ({
            pendaftar: 0,
            aktif: 0,
            lulus: 0,
        }),
    },
})


/*
|--------------------------------------------------------------------------
| STATISTIK UTAMA
|--------------------------------------------------------------------------
*/

const statItems = [
    {
        key: 'peserta',
        title: 'Total Peserta',
        description: 'Seluruh peserta terdaftar',
        icon: 'users',
        color: 'blue',
    },
    {
        key: 'instruktur',
        title: 'Instruktur',
        description: 'Instruktur pelatihan',
        icon: 'teacher',
        color: 'violet',
    },
    {
        key: 'kelas',
        title: 'Kelas Aktif',
        description: 'Kelas sedang berjalan',
        icon: 'class',
        color: 'emerald',
    },
    {
        key: 'kelulusan',
        title: 'Tingkat Kelulusan',
        description: 'Persentase kelulusan',
        icon: 'graduation',
        color: 'green',
    },
    {
        key: 'pendaftaran_baru',
        title: 'Pendaftaran Baru',
        description: 'Pendaftaran bulan ini',
        icon: 'user-plus',
        color: 'orange',
    },
    {
        key: 'sertifikasi',
        title: 'Sertifikasi',
        description: 'Sertifikat telah diterbitkan',
        icon: 'certificate',
        color: 'purple',
    },
]


/*
|--------------------------------------------------------------------------
| WARNA ICON
|--------------------------------------------------------------------------
*/

const iconClass = (color) => {
    const classes = {
        blue: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',

        violet: 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',

        emerald: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',

        red: 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400',

        green: 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400',

        orange: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',

        purple: 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400',
    }

    return classes[color] || classes.blue
}


/*
|--------------------------------------------------------------------------
| STATUS BADGE
|--------------------------------------------------------------------------
*/

const getStatusBadge = (status) => {
    const map = {
        aktif: 'success',
        selesai: 'info',
        ditunda: 'warning',
        dibatalkan: 'danger',

        menunggu: 'warning',
        diterima: 'success',
        ditolak: 'danger',
        proses: 'info',

        lulus: 'success',
        tidak_lulus: 'danger',
    }

    return map[String(status ?? '').toLowerCase()] || 'secondary'
}


/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

const formatDate = (date) => {
    if (!date) return '-'

    const d = new Date(date)

    if (Number.isNaN(d.getTime())) {
        return '-'
    }

    return d.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}


/*
|--------------------------------------------------------------------------
| FORMAT WAKTU
|--------------------------------------------------------------------------
*/

const formatTime = (date) => {
    if (!date) return '-'

    const d = new Date(date)

    if (Number.isNaN(d.getTime())) {
        return '-'
    }

    return d.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    })
}


/*
|--------------------------------------------------------------------------
| PERSENTASE PRESENSI
|--------------------------------------------------------------------------
|
| Contoh:
| total peserta = 20
| hadir = 15
| hasil = 75%
|
*/

const attendancePercentage = (value) => {
    const total = Number(props.presensi?.total_peserta ?? 0)
    const jumlah = Number(value ?? 0)

    if (total <= 0) {
        return 0
    }

    return Math.min(
        100,
        Math.round((jumlah / total) * 100)
    )
}


/*
|--------------------------------------------------------------------------
| TOTAL PRESENSI TERCATAT
|--------------------------------------------------------------------------
*/

const totalPresensi = computed(() => {
    return (
        Number(props.presensi?.hadir ?? 0) +
        Number(props.presensi?.izin ?? 0) +
        Number(props.presensi?.sakit ?? 0) +
        Number(props.presensi?.alpha ?? 0)
    )
})


/*
|--------------------------------------------------------------------------
| PERSENTASE PRESENSI KESELURUHAN
|--------------------------------------------------------------------------
*/

const persentaseHadir = computed(() => {
    return attendancePercentage(
        props.presensi?.hadir
    )
})
</script>


<template>

    <Head title="Dashboard BLK Multimedia" />

    <div class="space-y-6">


        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >

            <div>
                <h1
                    class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white"
                >
                    Dashboard BLK Multimedia
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Ringkasan informasi dan aktivitas Balai Latihan Kerja
                    Komunitas Multimedia.
                </p>
            </div>


            <div class="flex flex-wrap items-center gap-3">

                <!-- Tanggal -->
                <div
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"
                >
                    {{
                        new Date().toLocaleDateString('id-ID', {
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        })
                    }}
                </div>


                <!-- Status -->
                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400"
                >
                    <span class="mr-1">●</span>
                    Sistem Aktif
                </div>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- STATISTIK UTAMA -->
        <!-- ========================================================= -->

        <div
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6"
        >

            <BaseCard
                v-for="item in statItems"
                :key="item.key"
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p
                            class="text-sm font-medium text-slate-500 dark:text-slate-400"
                        >
                            {{ item.title }}
                        </p>

                        <p
                            class="mt-2 text-3xl font-bold text-slate-900 dark:text-white"
                        >
                            {{ stats?.[item.key] ?? 0 }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            {{ item.description }}
                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl"
                        :class="iconClass(item.color)"
                    >

                        <!-- USERS -->
                        <svg
                            v-if="item.icon === 'users'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"
                            />

                            <circle
                                cx="9"
                                cy="7"
                                r="4"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"
                            />
                        </svg>


                        <!-- TEACHER -->
                        <svg
                            v-else-if="item.icon === 'teacher'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14l9-5-9-5-9 5 9 5z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14v7"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12v4c0 1.5 3.1 3 7 3s7-1.5 7-3v-4"
                            />
                        </svg>


                        <!-- CLASS -->
                        <svg
                            v-else-if="item.icon === 'class'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 19.5A2.5 2.5 0 016.5 17H20"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"
                            />
                        </svg>


                        <!-- GRADUATION -->
                        <svg
                            v-else-if="item.icon === 'graduation'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14l9-5-9-5-9 5 9 5z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14l6-3.33V10"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14v4"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 12v4c0 1.5 3.1 3 7 3s7-1.5 7-3v-4"
                            />
                        </svg>


                        <!-- USER PLUS -->
                        <svg
                            v-else-if="item.icon === 'user-plus'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 20a6 6 0 0112 0v1H3v-1z"
                            />
                        </svg>


                        <!-- CERTIFICATE -->
                        <svg
                            v-else-if="item.icon === 'certificate'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 4a9 9 0 11-18 0 9 9 0 0118 0z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 8v4l2 2"
                            />
                        </svg>

                    </div>

                </div>

            </BaseCard>

        </div>



        <!-- ========================================================= -->
        <!-- STATISTIK BULANAN -->
        <!-- ========================================================= -->

        <div class="grid gap-4 sm:grid-cols-3">

            <BaseCard>

                <div class="text-center">

                    <p
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Pendaftar Bulan Ini
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400"
                    >
                        {{ statistikBulanan?.pendaftar ?? 0 }}
                    </p>

                </div>

            </BaseCard>


            <BaseCard>

                <div class="text-center">

                    <p
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Peserta Aktif
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400"
                    >
                        {{ statistikBulanan?.aktif ?? 0 }}
                    </p>

                </div>

            </BaseCard>


            <BaseCard>

                <div class="text-center">

                    <p
                        class="text-sm font-medium text-slate-500 dark:text-slate-400"
                    >
                        Lulus Bulan Ini
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-400"
                    >
                        {{ statistikBulanan?.lulus ?? 0 }}
                    </p>

                </div>

            </BaseCard>

        </div>



        <!-- ========================================================= -->
        <!-- PRESENSI + KELAS AKTIF -->
        <!-- ========================================================= -->

        <div class="grid gap-6 xl:grid-cols-2">


            <!-- ===================================================== -->
            <!-- PRESENSI -->
            <!-- ===================================================== -->

            <BaseCard
                title="Ringkasan Presensi Hari Ini"
                description="Rekap presensi peserta pelatihan hari ini."
            >

                <div class="space-y-5">


                    <!-- TOTAL -->
                    <div
                        class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800/50"
                    >

                        <div>
                            <p
                                class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                Peserta Aktif
                            </p>

                            <p class="text-xs text-slate-400">
                                Peserta yang mengikuti pelatihan
                            </p>
                        </div>

                        <span
                            class="text-xl font-bold text-slate-900 dark:text-white"
                        >
                            {{ presensi?.total_peserta ?? 0 }}
                        </span>

                    </div>


                    <!-- HADIR -->
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span
                                class="text-sm text-slate-600 dark:text-slate-300"
                            >
                                Hadir
                            </span>

                            <span
                                class="font-semibold text-slate-900 dark:text-white"
                            >
                                {{ presensi?.hadir ?? 0 }}
                                <span class="ml-1 text-xs font-normal text-slate-400">
                                    ({{ attendancePercentage(presensi?.hadir) }}%)
                                </span>
                            </span>

                        </div>


                        <div
                            class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"
                        >

                            <div
                                class="h-full rounded-full bg-emerald-500 transition-all duration-500"
                                :style="{
                                    width: `${attendancePercentage(presensi?.hadir)}%`
                                }"
                            ></div>

                        </div>

                    </div>


                    <!-- IZIN -->
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span
                                class="text-sm text-slate-600 dark:text-slate-300"
                            >
                                Izin
                            </span>

                            <span
                                class="font-semibold text-slate-900 dark:text-white"
                            >
                                {{ presensi?.izin ?? 0 }}

                                <span class="ml-1 text-xs font-normal text-slate-400">
                                    ({{ attendancePercentage(presensi?.izin) }}%)
                                </span>
                            </span>

                        </div>


                        <div
                            class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"
                        >

                            <div
                                class="h-full rounded-full bg-blue-500 transition-all duration-500"
                                :style="{
                                    width: `${attendancePercentage(presensi?.izin)}%`
                                }"
                            ></div>

                        </div>

                    </div>


                    <!-- SAKIT -->
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span
                                class="text-sm text-slate-600 dark:text-slate-300"
                            >
                                Sakit
                            </span>

                            <span
                                class="font-semibold text-slate-900 dark:text-white"
                            >
                                {{ presensi?.sakit ?? 0 }}

                                <span class="ml-1 text-xs font-normal text-slate-400">
                                    ({{ attendancePercentage(presensi?.sakit) }}%)
                                </span>
                            </span>

                        </div>


                        <div
                            class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"
                        >

                            <div
                                class="h-full rounded-full bg-yellow-500 transition-all duration-500"
                                :style="{
                                    width: `${attendancePercentage(presensi?.sakit)}%`
                                }"
                            ></div>

                        </div>

                    </div>


                    <!-- ALPHA -->
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span
                                class="text-sm text-slate-600 dark:text-slate-300"
                            >
                                Alpha
                            </span>

                            <span
                                class="font-semibold text-slate-900 dark:text-white"
                            >
                                {{ presensi?.alpha ?? 0 }}

                                <span class="ml-1 text-xs font-normal text-slate-400">
                                    ({{ attendancePercentage(presensi?.alpha) }}%)
                                </span>
                            </span>

                        </div>


                        <div
                            class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"
                        >

                            <div
                                class="h-full rounded-full bg-red-500 transition-all duration-500"
                                :style="{
                                    width: `${attendancePercentage(presensi?.alpha)}%`
                                }"
                            ></div>

                        </div>

                    </div>


                    <!-- SUMMARY -->
                    <div
                        class="border-t border-slate-100 pt-4 dark:border-slate-800"
                    >

                        <div class="flex items-center justify-between">

                            <span class="text-xs text-slate-400">
                                Total presensi tercatat
                            </span>

                            <span
                                class="text-sm font-semibold text-slate-700 dark:text-slate-200"
                            >
                                {{ totalPresensi }}
                            </span>

                        </div>

                    </div>

                </div>

            </BaseCard>



            <!-- ===================================================== -->
            <!-- KELAS AKTIF -->
            <!-- ===================================================== -->

            <BaseCard
                title="Kelas Pelatihan Aktif"
                description="Kelas yang sedang berjalan."
            >

                <div
                    v-if="kelasAktif.length"
                    class="divide-y divide-slate-100 dark:divide-slate-800"
                >

                    <div
                        v-for="kelas in kelasAktif"
                        :key="kelas.id"
                        class="py-4 first:pt-0 last:pb-0"
                    >

                        <div
                            class="flex items-center justify-between gap-4"
                        >

                            <div class="min-w-0">

                                <p
                                    class="truncate text-sm font-semibold text-slate-800 dark:text-white"
                                >
                                    {{ kelas.nama }}
                                </p>

                                <p
                                    class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ kelas.instruktur?.nama || 'Instruktur: -' }}
                                    •
                                    {{ kelas.peserta_count || 0 }}
                                    peserta
                                </p>

                            </div>


                            <BaseBadge
                                :variant="getStatusBadge(kelas.status)"
                            >
                                {{ kelas.status || 'Aktif' }}
                            </BaseBadge>

                        </div>


                        <div
                            class="mt-2 flex items-center gap-2 text-xs text-slate-400"
                        >

                            <span>
                                Mulai:
                                {{ formatDate(kelas.tanggal_mulai) }}
                            </span>

                            <span>•</span>

                            <span>
                                Selesai:
                                {{ formatDate(kelas.tanggal_selesai) }}
                            </span>

                        </div>

                    </div>

                </div>


                <!-- EMPTY -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center py-10 text-center"
                >

                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 text-slate-400 dark:bg-slate-800"
                    >
                        📚
                    </div>

                    <p
                        class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Belum ada kelas aktif
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Kelas pelatihan akan muncul di sini.
                    </p>

                </div>

            </BaseCard>

        </div>



        <!-- ========================================================= -->
        <!-- PENDAFTARAN + KEGIATAN -->
        <!-- ========================================================= -->

        <div class="grid gap-6 xl:grid-cols-2">


            <!-- ===================================================== -->
            <!-- PENDAFTARAN TERBARU -->
            <!-- ===================================================== -->

            <BaseCard
                title="Pendaftaran Terbaru"
                description="Peserta yang baru mendaftar."
            >

                <div
                    v-if="pendaftaranTerbaru.length"
                    class="divide-y divide-slate-100 dark:divide-slate-800"
                >

                    <div
                        v-for="pendaftar in pendaftaranTerbaru"
                        :key="pendaftar.id"
                        class="py-4 first:pt-0 last:pb-0"
                    >

                        <div
                            class="flex items-center justify-between gap-4"
                        >

                            <div class="min-w-0">

                                <p
                                    class="truncate text-sm font-semibold text-slate-800 dark:text-white"
                                >
                                    {{ pendaftar.nama }}
                                </p>

                                <p
                                    class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ pendaftar.program_pelatihan?.pelatihan || '-' }}

                                    <span> • </span>

                                    {{ pendaftar.program_pelatihan?.tahap || '-' }}
                                </p>

                                <p
                                    class="mt-1 truncate text-xs text-slate-400"
                                >
                                    {{ pendaftar.kelas?.nama || 'Kelas: -' }}

                                    <span> • </span>

                                    {{ formatDate(pendaftar.tanggal_daftar) }}
                                </p>

                            </div>


                            <BaseBadge
                                :variant="getStatusBadge(pendaftar.status)"
                            >
                                {{ pendaftar.status || 'Menunggu' }}
                            </BaseBadge>

                        </div>

                    </div>

                </div>


                <!-- EMPTY -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center py-10 text-center"
                >

                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-500 dark:bg-blue-500/10"
                    >
                        📝
                    </div>

                    <p
                        class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Belum ada pendaftaran baru
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Pendaftaran akan muncul di sini.
                    </p>

                </div>

            </BaseCard>



            <!-- ===================================================== -->
            <!-- KEGIATAN TERBARU -->
            <!-- ===================================================== -->

            <BaseCard
                title="Kegiatan Terbaru"
                description="Aktivitas terkini dalam sistem BLK."
            >

                <div
                    v-if="kegiatanTerbaru.length"
                    class="divide-y divide-slate-100 dark:divide-slate-800"
                >

                    <div
                        v-for="kegiatan in kegiatanTerbaru"
                        :key="kegiatan.id"
                        class="flex items-start gap-4 py-4 first:pt-0 last:pb-0"
                    >

                        <div
                            class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm dark:bg-slate-800"
                        >
                            {{ kegiatan.ikon || '📌' }}
                        </div>


                        <div class="min-w-0">

                            <p
                                class="text-sm font-semibold text-slate-800 dark:text-white"
                            >
                                {{ kegiatan.judul }}
                            </p>

                            <p
                                class="mt-0.5 line-clamp-2 text-xs text-slate-500 dark:text-slate-400"
                            >
                                {{ kegiatan.deskripsi }}
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                {{ formatDate(kegiatan.tanggal) }}
                                <span v-if="kegiatan.tanggal">
                                    •
                                    {{ formatTime(kegiatan.tanggal) }}
                                </span>
                            </p>

                        </div>

                    </div>

                </div>


                <!-- EMPTY -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center py-10 text-center"
                >

                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 text-purple-500 dark:bg-purple-500/10"
                    >
                        🎯
                    </div>

                    <p
                        class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Belum ada kegiatan
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Aktivitas sistem akan muncul di sini.
                    </p>

                </div>

            </BaseCard>

        </div>



        <!-- ========================================================= -->
        <!-- WELCOME -->
        <!-- ========================================================= -->

        <BaseCard>

            <div
                class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
            >

                <div>

                    <p
                        class="text-sm font-medium text-blue-600 dark:text-blue-400"
                    >
                        BLK KOMUNITAS MULTIMEDIA
                    </p>

                    <h2
                        class="mt-1 text-xl font-bold text-slate-900 dark:text-white"
                    >
                        Selamat datang di Dashboard BLK Multimedia
                    </h2>

                    <p
                        class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400"
                    >
                        Kelola pendaftaran peserta, program pelatihan,
                        kelas, jadwal, presensi, sertifikasi, dan laporan
                        secara terpusat.
                    </p>

                </div>


                <div
                    class="flex shrink-0 items-center gap-3"
                >

                    <div
                        class="flex h-20 w-20 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-10 w-10"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>

                    </div>


                    <div class="hidden text-right sm:block">

                        <p class="text-xs text-slate-400">
                            Versi
                        </p>

                        <p
                            class="text-sm font-semibold text-slate-800 dark:text-white"
                        >
                            2.0.0
                        </p>

                    </div>

                </div>

            </div>

        </BaseCard>

    </div>

</template>