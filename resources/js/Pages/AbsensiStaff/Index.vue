<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    absensis: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            meta: {},
        }),
    },

    users: {
        type: Array,
        default: () => [],
    },

    lokasis: {
        type: Array,
        default: () => [],
    },

    stats: {
        type: Object,
        default: () => ({
            total: 0,
            hadir: 0,
            terlambat: 0,
            izin: 0,
            sakit: 0,
            dinas: 0,
            alpha: 0,
        }),
    },

    filters: {
        type: Object,
        default: () => ({
            tanggal: '',
            user_id: '',
            status: '',
        }),
    },
})

const tanggal = ref(props.filters.tanggal ?? '')
const userId = ref(props.filters.user_id ?? '')
const status = ref(props.filters.status ?? '')

const applyFilter = () => {
    router.get(
        route('absensi-staff.index'),
        {
            tanggal: tanggal.value,
            user_id: userId.value,
            status: status.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

const resetFilter = () => {
    tanggal.value = ''
    userId.value = ''
    status.value = ''

    router.get(
        route('absensi-staff.index'),
        {},
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

const deleteAbsensi = (absensi) => {
    if (
        !confirm(
            `Hapus data absensi ${absensi.user?.name ?? 'staff'}?`
        )
    ) {
        return
    }

    router.delete(
        route('absensi-staff.destroy', absensi.id),
        {
            preserveScroll: true,
        }
    )
}

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    const date = new Date(tanggal)

    if (isNaN(date.getTime())) return '-'

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        timeZone: 'Asia/Jakarta',
    })
}

const formatJam = (waktu) => {
    if (!waktu) return '-'

    const date = new Date(waktu)

    if (isNaN(date.getTime())) return '-'

    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
        timeZone: 'Asia/Jakarta',
    })
}

const formatJarak = (jarak) => {
    if (
        jarak === null ||
        jarak === undefined ||
        jarak === ''
    ) {
        return '-'
    }

    return `${Number(jarak).toFixed(2)} m`
}

const formatScore = (score) => {
    if (
        score === null ||
        score === undefined ||
        score === ''
    ) {
        return '-'
    }

    return `${Number(score).toFixed(2)}%`
}

const statusClass = (value) => {
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
        classes[value] ??
        'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
    )
}

const statusLabel = (value) => {
    const labels = {
        hadir: 'Hadir',
        terlambat: 'Terlambat',
        izin: 'Izin',
        sakit: 'Sakit',
        dinas: 'Dinas',
        alpha: 'Alpha',
    }

    return labels[value] ?? value ?? '-'
}

const paginationLinks = computed(() => {
    return props.absensis?.links ?? []
})
</script>

<template>
    <Head title="Absensi Staff" />

    <div class="space-y-6">
        
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-slate-800 dark:text-white"
                >
                    Absensi Staff
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Monitoring kehadiran, lokasi, waktu, dan
                    verifikasi wajah staff.
                </p>
            </div>

            <Link
                :href="route('absensi-staff.absen')"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
            >
                📷 Absen Staff
            </Link>
        </div>


        <!-- STATISTIK -->
        <div
            class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-7"
        >
            <!-- TOTAL -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                >
                    Total
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-slate-800 dark:text-white"
                >
                    {{ stats.total }}
                </p>
            </div>

            <!-- HADIR -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-emerald-600"
                >
                    Hadir
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-slate-800 dark:text-white"
                >
                    {{ stats.hadir }}
                </p>
            </div>

            <!-- TERLAMBAT -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-amber-600"
                >
                    Terlambat
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-slate-800 dark:text-white"
                >
                    {{ stats.terlambat }}
                </p>
            </div>

            <!-- IZIN -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-blue-600"
                >
                    Izin
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-slate-800 dark:text-white"
                >
                    {{ stats.izin }}
                </p>
            </div>

            <!-- SAKIT -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-purple-600"
                >
                    Sakit
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-slate-800 dark:text-white"
                >
                    {{ stats.sakit }}
                </p>
            </div>

            <!-- DINAS -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-indigo-600"
                >
                    Dinas
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-slate-800 dark:text-white"
                >
                    {{ stats.dinas }}
                </p>
            </div>

            <!-- ALPHA -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-red-600"
                >
                    Alpha
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-slate-800 dark:text-white"
                >
                    {{ stats.alpha }}
                </p>
            </div>
        </div>

        <!-- FILTER -->
        <div
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div
                class="grid grid-cols-1 gap-4 md:grid-cols-4"
            >

                <!-- TANGGAL -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Tanggal
                    </label>

                    <input
                        v-model="tanggal"
                        type="date"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    />
                </div>

                <!-- STAFF -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Staff
                    </label>

                    <select
                        v-model="userId"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >
                        <option value="">
                            Semua Staff
                        </option>

                        <option
                            v-for="user in users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </select>
                </div>

                <!-- STATUS -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Status
                    </label>

                    <select
                        v-model="status"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >
                        <option value="">
                            Semua Status
                        </option>

                        <option value="hadir">
                            Hadir
                        </option>

                        <option value="terlambat">
                            Terlambat
                        </option>

                        <option value="izin">
                            Izin
                        </option>

                        <option value="sakit">
                            Sakit
                        </option>

                        <option value="dinas">
                            Dinas
                        </option>

                        <option value="alpha">
                            Alpha
                        </option>
                    </select>
                </div>

                <!-- BUTTON -->
                <div class="flex items-end gap-2">
                    <button
                        type="button"
                        @click="applyFilter"
                        class="flex-1 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                    >
                        Terapkan
                    </button>

                    <button
                        type="button"
                        @click="resetFilter"
                        class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div
                class="overflow-x-auto"
            >
                <table class="w-full min-w-[1100px] text-left">
                    <thead
                        class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800"
                    >
                        <tr>
                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Staff
                            </th>

                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Tanggal
                            </th>

                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Masuk
                            </th>

                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Pulang
                            </th>

                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Lokasi
                            </th>

                            <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Jarak
                            </th>

                            <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        class="divide-y divide-slate-200 dark:divide-slate-700"
                    >
                        <tr
                            v-for="absensi in absensis.data"
                            :key="absensi.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        >
                            <!-- STAFF -->
                            <td class="px-5 py-4">
                                <div>
                                    <p
                                        class="font-semibold text-slate-800 dark:text-white"
                                    >
                                        {{ absensi.user?.name ?? '-' }}
                                    </p>

                                    <p
                                        class="text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        @{{ absensi.user?.username ?? '-' }}
                                    </p>
                                </div>
                            </td>

                            <!-- TANGGAL -->
                            <td class="px-5 py-4 text-sm text-slate-700 dark:text-slate-300">
                                {{ formatTanggal(absensi.tanggal) }}
                            </td>

                            <!-- MASUK -->
                            <td class="px-5 py-4">
                                <span
                                    class="font-semibold text-slate-800 dark:text-white"
                                >
                                    {{ formatJam(absensi.waktu_masuk) }}
                                </span>

                                <p
                                    v-if="absensi.face_score_masuk"
                                    class="mt-1 text-xs text-slate-500"
                                >
                                    Face {{ formatScore(absensi.face_score_masuk) }}
                                </p>
                            </td>

                            <!-- PULANG -->
                            <td class="px-5 py-4">
                                <span
                                    class="font-semibold text-slate-800 dark:text-white"
                                >
                                    {{ formatJam(absensi.waktu_keluar) }}
                                </span>

                                <p
                                    v-if="absensi.face_score_keluar"
                                    class="mt-1 text-xs text-slate-500"
                                >
                                    Face {{ formatScore(absensi.face_score_keluar) }}
                                </p>
                            </td>

                            <!-- STATUS -->
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-bold"
                                    :class="statusClass(absensi.status)"
                                >
                                    {{ statusLabel(absensi.status) }}
                                </span>
                            </td>

                            <!-- LOKASI -->
                            <td class="px-5 py-4">
                                <p
                                    class="text-sm font-medium text-slate-700 dark:text-slate-300"
                                >
                                    {{ absensi.lokasi_absensi?.nama ?? '-' }}
                                </p>
                            </td>

                            <!-- JARAK -->
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ formatJarak(absensi.jarak_masuk) }}
                            </td>

                            <!-- AKSI -->
                            <td class="px-5 py-4">
                                <div class="flex justify-center gap-2">

                                    <Link
                                        :href="route('absensi-staff.show', absensi.id)"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                    >
                                        Detail
                                    </Link>

                                    <button
                                        type="button"
                                        @click="deleteAbsensi(absensi)"
                                        class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900/50 dark:bg-slate-800 dark:hover:bg-red-950/30"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- EMPTY -->
                        <tr
                            v-if="!absensis.data?.length"
                        >
                            <td
                                colspan="8"
                                class="px-5 py-12 text-center"
                            >
                                <div
                                    class="text-4xl"
                                >
                                    📋
                                </div>

                                <p
                                    class="mt-3 font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Belum ada data absensi
                                </p>

                                <p
                                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Belum ada absensi staff pada
                                    tanggal yang dipilih.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div
                v-if="paginationLinks.length > 3"
                class="flex flex-wrap items-center justify-center gap-2 border-t border-slate-200 px-5 py-4 dark:border-slate-700"
            >
                <template
                    v-for="(link, index) in paginationLinks"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        preserve-scroll
                        class="rounded-lg border px-3 py-2 text-xs font-semibold transition"
                        :class="
                            link.active
                                ? 'border-indigo-600 bg-indigo-600 text-white'
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200'
                        "
                    >
                        <span v-html="link.label"></span>
                    </Link>

                    <span
                        v-else
                        class="rounded-lg border border-slate-100 px-3 py-2 text-xs text-slate-400 dark:border-slate-800"
                    >
                        <span v-html="link.label"></span>
                    </span>
                </template>
            </div>
        </div>
    </div>
</template>