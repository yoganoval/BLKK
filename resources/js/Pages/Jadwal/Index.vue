<script setup>
import { computed, ref } from 'vue'
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

const search = ref('')
const statusFilter = ref('')

/*
|--------------------------------------------------------------------------
| Helper Kelas
|--------------------------------------------------------------------------
*/

const getNamaKelas = (jadwal) => {
    if (!jadwal?.kelas || !Array.isArray(jadwal.kelas)) {
        return ''
    }

    return jadwal.kelas
        .map(kelas => kelas?.nama ?? '')
        .filter(Boolean)
        .join(', ')
}

/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/

const filteredJadwals = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    return props.jadwals.filter((item) => {
        const program = item.program_pelatihan?.nama ?? ''
        const pelatihan = item.program_pelatihan?.pelatihan?.nama ?? ''
        const tahap = item.program_pelatihan?.tahap?.nama ?? ''
        const kelas = getNamaKelas(item)
        const kegiatan = item.kegiatan ?? ''

        const matchesSearch =
            !keyword ||
            program.toLowerCase().includes(keyword) ||
            pelatihan.toLowerCase().includes(keyword) ||
            tahap.toLowerCase().includes(keyword) ||
            kelas.toLowerCase().includes(keyword) ||
            kegiatan.toLowerCase().includes(keyword)

        const matchesStatus =
            !statusFilter.value ||
            item.status === statusFilter.value

        return matchesSearch && matchesStatus
    })
})

/*
|--------------------------------------------------------------------------
| Statistik
|--------------------------------------------------------------------------
*/

const totalJadwal = computed(() => props.jadwals.length)

const jadwalBerlangsung = computed(() =>
    props.jadwals.filter(item => item.status === 'berlangsung').length
)

const jadwalBelumDimulai = computed(() =>
    props.jadwals.filter(item => item.status === 'belum_dimulai').length
)

const jadwalSelesai = computed(() =>
    props.jadwals.filter(item => item.status === 'selesai').length
)

/*
|--------------------------------------------------------------------------
| Format
|--------------------------------------------------------------------------
*/

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

const formatJam = (jam) => {
    if (!jam) return '-'

    return String(jam).substring(0, 5)
}

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

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

    return classes[status] ??
        'bg-slate-50 text-slate-600 ring-slate-600/20 dark:bg-slate-800 dark:text-slate-400'
}

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const confirmDelete = (jadwal) => {
    const program = jadwal.program_pelatihan?.nama ?? 'Jadwal'
    const kelas = getNamaKelas(jadwal)

    const namaJadwal = kelas
        ? `${program} - ${kelas}`
        : program

    if (
        confirm(
            `Yakin ingin menghapus jadwal "${namaJadwal}" pertemuan ${jadwal.pertemuan}?`
        )
    ) {
        router.delete(route('jadwals.destroy', jadwal.id), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head title="Jadwal" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
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
                                Jadwal
                            </h1>

                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Kelola jadwal kegiatan pelatihan
                            </p>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('jadwals.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950"
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
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    Tambah Jadwal
                </Link>
            </div>

            <!-- STATISTIK -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <!-- Total -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Total Jadwal
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                                {{ totalJadwal }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-indigo-50 p-3 dark:bg-indigo-400/10">
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
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Belum Dimulai -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Belum Dimulai
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                                {{ jadwalBelumDimulai }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-amber-50 p-3 dark:bg-amber-400/10">
                            <svg
                                class="h-6 w-6 text-amber-600 dark:text-amber-400"
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
                    </div>
                </div>

                <!-- Berlangsung -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Berlangsung
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                                {{ jadwalBerlangsung }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-indigo-50 p-3 dark:bg-indigo-400/10">
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
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.868v4.264a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Selesai -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                Selesai
                            </p>

                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                                {{ jadwalSelesai }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-emerald-50 p-3 dark:bg-emerald-400/10">
                            <svg
                                class="h-6 w-6 text-emerald-600 dark:text-emerald-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- TABLE CARD -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >

                <!-- FILTER -->
                <div class="border-b border-slate-200 p-5 dark:border-slate-800">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

                        <div class="relative md:col-span-2">
                            <svg
                                class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"
                                />
                            </svg>

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari program, pelatihan, tahap, kelas..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                        </div>

                        <select
                            v-model="statusFilter"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >
                            <option value="">Semua Status</option>
                            <option value="belum_dimulai">Belum Dimulai</option>
                            <option value="berlangsung">Berlangsung</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>

                    </div>
                </div>

                <!-- EMPTY -->
                <div
                    v-if="filteredJadwals.length === 0"
                    class="px-6 py-16 text-center"
                >
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800"
                    >
                        <svg
                            class="h-8 w-8 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"
                            />
                        </svg>
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-slate-900 dark:text-white">
                        Tidak ada jadwal
                    </h3>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Belum ada data jadwal yang sesuai dengan pencarian.
                    </p>
                </div>

                <!-- TABLE -->
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">

                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th
                                    class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Program
                                </th>

                                <th
                                    class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Kelas
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Pertemuan
                                </th>

                                <th
                                    class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Tanggal
                                </th>

                                <th
                                    class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Waktu
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Status
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                            <tr
                                v-for="jadwal in filteredJadwals"
                                :key="jadwal.id"
                                class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40"
                            >

                                <!-- PROGRAM -->
                                <td class="px-5 py-4">
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white">
                                            {{ jadwal.program_pelatihan?.nama ?? '-' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            {{ jadwal.program_pelatihan?.pelatihan?.nama ?? '-' }}

                                            <span v-if="jadwal.program_pelatihan?.tahap">
                                                · {{ jadwal.program_pelatihan.tahap.nama }}
                                            </span>
                                        </p>
                                    </div>
                                </td>

                                <!-- KELAS -->
                                <td class="px-5 py-4">
                                    <div
                                        v-if="jadwal.kelas?.length"
                                        class="flex max-w-xs flex-wrap gap-1.5"
                                    >
                                        <span
                                            v-for="kelas in jadwal.kelas"
                                            :key="kelas.id"
                                            class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                        >
                                            {{ kelas.nama }}
                                        </span>
                                    </div>

                                    <span
                                        v-else
                                        class="text-sm text-slate-400"
                                    >
                                        -
                                    </span>
                                </td>

                                <!-- PERTEMUAN -->
                                <td class="px-5 py-4 text-center">
                                    <span class="font-bold text-slate-900 dark:text-white">
                                        {{ jadwal.pertemuan }}
                                    </span>
                                </td>

                                <!-- TANGGAL -->
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ formatTanggal(jadwal.tanggal) }}
                                </td>

                                <!-- WAKTU -->
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                        {{ formatJam(jadwal.jam_mulai) }}

                                        <span class="text-slate-400">
                                            -
                                        </span>

                                        {{ formatJam(jadwal.jam_selesai) }}
                                    </div>
                                </td>

                                <!-- STATUS -->
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset"
                                        :class="statusClass(jadwal.status)"
                                    >
                                        {{ statusLabel(jadwal.status) }}
                                    </span>
                                </td>

                                <!-- AKSI -->
                                <td class="px-5 py-4">
                                    <div class="flex justify-center gap-2">

                                        <Link
                                            :href="route('jadwals.show', jadwal.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-indigo-400/10 dark:hover:text-indigo-400"
                                        >
                                            Detail
                                        </Link>

                                        <Link
                                            :href="route('jadwals.edit', jadwal.id)"
                                            class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-400/20 dark:bg-indigo-400/10 dark:text-indigo-400"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            type="button"
                                            @click="confirmDelete(jadwal)"
                                            class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-400"
                                        >
                                            Hapus
                                        </button>

                                    </div>
                                </td>

                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- FOOTER -->
                <div
                    v-if="filteredJadwals.length > 0"
                    class="border-t border-slate-200 px-5 py-4 dark:border-slate-800"
                >
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Menampilkan

                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                            {{ filteredJadwals.length }}
                        </span>

                        dari

                        <span class="font-semibold text-slate-700 dark:text-slate-200">
                            {{ jadwals.length }}
                        </span>

                        jadwal
                    </p>
                </div>

            </div>
        </div>
    </div>
</template>