<script setup>
import { computed, ref } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    absensis: {
        type: Array,
        default: () => [],
    },
})

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

const search = ref('')

/*
|--------------------------------------------------------------------------
| FILTER PELATIHAN
|--------------------------------------------------------------------------
*/

const pelatihanFilter = ref('all')

/*
|--------------------------------------------------------------------------
| FILTER TAHAP
|--------------------------------------------------------------------------
*/

const tahapFilter = ref('all')

/*
|--------------------------------------------------------------------------
| DATA FILTERED
|--------------------------------------------------------------------------
*/

const filteredAbsensis = computed(() => {

    let data = [...props.absensis]

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if (search.value.trim()) {

        const keyword =
            search.value
                .toLowerCase()
                .trim()

        data = data.filter(absensi => {

            const peserta =
                absensi.pendaftaran?.peserta?.nama
                ?.toLowerCase() ?? ''

            const pelatihan =
                absensi.jadwal
                    ?.program_pelatihan
                    ?.pelatihan
                    ?.nama
                    ?.toLowerCase() ?? ''

            const tahap =
                absensi.jadwal
                    ?.program_pelatihan
                    ?.tahap
                    ?.nama
                    ?.toLowerCase() ?? ''

            const kelas =
                absensi.pendaftaran
                    ?.kelas
                    ?.nama
                    ?.toLowerCase() ?? ''

            return (
                peserta.includes(keyword) ||
                pelatihan.includes(keyword) ||
                tahap.includes(keyword) ||
                kelas.includes(keyword)
            )
        })
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER PELATIHAN
    |--------------------------------------------------------------------------
    */

    if (pelatihanFilter.value !== 'all') {

        data = data.filter(absensi => {

            return (
                absensi.jadwal
                    ?.program_pelatihan
                    ?.pelatihan
                    ?.id
                == pelatihanFilter.value
            )
        })
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER TAHAP
    |--------------------------------------------------------------------------
    */

    if (tahapFilter.value !== 'all') {

        data = data.filter(absensi => {

            return (
                absensi.jadwal
                    ?.program_pelatihan
                    ?.tahap
                    ?.id
                == tahapFilter.value
            )
        })
    }

    return data
})


/*
|--------------------------------------------------------------------------
| DAFTAR PELATIHAN
|--------------------------------------------------------------------------
*/

const pelatihans = computed(() => {

    const map = new Map()

    props.absensis.forEach(absensi => {

        const pelatihan =
            absensi.jadwal
                ?.program_pelatihan
                ?.pelatihan

        if (pelatihan?.id) {
            map.set(
                pelatihan.id,
                pelatihan.nama
            )
        }
    })

    return Array.from(map, ([id, nama]) => ({
        id,
        nama,
    }))
})

/*
|--------------------------------------------------------------------------
| DAFTAR TAHAP
|--------------------------------------------------------------------------
*/

const tahaps = computed(() => {

    const map = new Map()

    props.absensis.forEach(absensi => {

        const tahap =
            absensi.jadwal
                ?.program_pelatihan
                ?.tahap

        if (tahap?.id) {
            map.set(
                tahap.id,
                tahap.nama
            )
        }
    })

    return Array.from(map, ([id, nama]) => ({
        id,
        nama,
    }))
})

/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

const totalAbsensi = computed(() => {

    return props.absensis.length
})

const totalHadir = computed(() => {

    return props.absensis.filter(
        item =>
            item.status === 'hadir'
    ).length
})

const totalFace = computed(() => {

    return props.absensis.filter(
        item =>
            item.metode === 'face'
    ).length
})

/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

const formatTanggal = (
    value
) => {

    if (!value) {
        return '-'
    }

    const date =
        new Date(value)

    if (
        isNaN(
            date.getTime()
        )
    ) {
        return value
    }

    return date.toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }
    )
}

/*
|--------------------------------------------------------------------------
| FORMAT JAM
|--------------------------------------------------------------------------
*/

const formatTime = (
    value
) => {

    if (!value) {
        return '-'
    }

    const date =
        new Date(value)

    if (
        isNaN(
            date.getTime()
        )
    ) {
        return String(value)
            .substring(0, 5)
    }

    return date.toLocaleTimeString(
        'id-ID',
        {
            hour: '2-digit',
            minute: '2-digit',
        }
    )
}

/*
|--------------------------------------------------------------------------
| RESET FILTER
|--------------------------------------------------------------------------
*/

const resetFilter = () => {

    search.value = ''

    statusFilter.value = 'all'

    metodeFilter.value = 'all'
}

const deleteAbsensi = (absensi) => {
    if (!confirm(`Hapus data absensi ${absensi.pendaftaran?.peserta?.nama ?? ''}?`)) {
        return
    }

    router.delete(route('rekap.absensi.destroy', absensi.id), {
        preserveScroll: true,
    })
}

</script>

<template>

    <Head title="Rekap Absensi" />

    <div class="space-y-6">

        <!-- =========================================================
             HEADER
        ========================================================== -->

        <div>

            <h1
                class="text-2xl font-bold text-slate-800 dark:text-white"
            >
                Rekap Absensi
            </h1>

            <p
                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
            >
                Daftar peserta yang sudah melakukan absensi.
            </p>

        </div>


        <!-- =========================================================
             STATISTIK
        ========================================================== -->

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-3"
        >

            <!-- TOTAL -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
            >

                <p
                    class="text-xs font-medium uppercase tracking-wide text-slate-400"
                >
                    Total Absensi
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-slate-800 dark:text-white"
                >
                    {{ totalAbsensi }}
                </p>

            </div>


            <!-- HADIR -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
            >

                <p
                    class="text-xs font-medium uppercase tracking-wide text-slate-400"
                >
                    Hadir
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-emerald-600"
                >
                    {{ totalHadir }}
                </p>

            </div>


            <!-- FACE -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
            >

                <p
                    class="text-xs font-medium uppercase tracking-wide text-slate-400"
                >
                    Face Recognition
                </p>

                <p
                    class="mt-2 text-2xl font-bold text-indigo-600"
                >
                    {{ totalFace }}
                </p>

            </div>

        </div>


        <!-- =========================================================
            FILTER
        ========================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
        >

            <div
                class="grid grid-cols-1 gap-4 md:grid-cols-4"
            >

                <!-- SEARCH -->

                <div class="md:col-span-2">

                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Cari Peserta
                    </label>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama, pelatihan, tahap, atau kelas..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                    />

                </div>


                <!-- PELATIHAN -->

                <div>

                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Pelatihan
                    </label>

                    <select
                        v-model="pelatihanFilter"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                    >

                        <option value="all">
                            Semua Pelatihan
                        </option>

                        <option
                            v-for="pelatihan in pelatihans"
                            :key="pelatihan.id"
                            :value="pelatihan.id"
                        >
                            {{ pelatihan.nama }}
                        </option>

                    </select>

                </div>


                <!-- TAHAP -->

                <div>

                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        Tahap
                    </label>

                    <select
                        v-model="tahapFilter"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                    >

                        <option value="all">
                            Semua Tahap
                        </option>

                        <option
                            v-for="tahap in tahaps"
                            :key="tahap.id"
                            :value="tahap.id"
                        >
                            {{ tahap.nama }}
                        </option>

                    </select>

                </div>

            </div>


            <!-- RESET -->

            <div class="mt-4 flex justify-end">

                <button
                    type="button"
                    @click="resetFilter"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600"
                >
                    Reset Filter
                </button>

            </div>

        </div>


        <!-- =========================================================
             TABLE
        ========================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800"
        >

            <!-- TABLE HEADER -->

            <div
                class="border-b border-slate-200 px-5 py-4 dark:border-slate-700"
            >

                <div
                    class="flex items-center justify-between"
                >

                    <div>

                        <h2
                            class="font-semibold text-slate-800 dark:text-white"
                        >
                            Data Absensi
                        </h2>

                        <p
                            class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                        >
                            Menampilkan
                            {{ filteredAbsensis.length }}
                            data
                        </p>

                    </div>

                </div>

            </div>


            <!-- TABLE -->

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1200px] text-left">
                <!-- HEADER -->
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>

                        <!-- NO -->
                        <th
                            class="w-16 px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            No
                        </th>

                        <!-- PESERTA -->
                        <th
                            class="min-w-[220px] px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            Peserta
                        </th>

                        <!-- PELATIHAN -->
                        <th
                            class="min-w-[220px] px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            Pelatihan
                        </th>

                        <!-- TAHAP -->
                        <th
                            class="w-32 px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            Tahap
                        </th>

                        <!-- KELAS -->
                        <th
                            class="min-w-[150px] px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            Kelas
                        </th>

                        <!-- PERTEMUAN -->
                        <th
                            class="w-32 px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            Pertemuan
                        </th>

                        <!-- WAKTU -->
                        <th
                            class="min-w-[150px] px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            Waktu
                        </th>

                        <!-- STATUS -->
                        <th
                            class="w-28 px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            Status
                        </th>

                        <!-- METODE -->
                        <th
                            class="w-28 px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            Metode
                        </th>

                        <!-- AKSI -->
                        <th
                            class="w-32 px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"
                        >
                            Aksi
                        </th>

                    </tr>
                </thead>


                <!-- BODY -->
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">

                    <!-- DATA -->
                    <tr
                        v-for="(absensi, index) in filteredAbsensis"
                        :key="absensi.id"
                        class="transition hover:bg-slate-50 dark:hover:bg-slate-700/30"
                    >

                        <!-- NO -->
                        <td
                            class="px-5 py-4 text-center text-sm font-medium text-slate-500 dark:text-slate-400"
                        >
                            {{ index + 1 }}
                        </td>


                        <!-- PESERTA -->
                        <td class="px-5 py-4">
                            <div class="min-w-0">

                                <p
                                    class="truncate font-semibold text-slate-800 dark:text-white"
                                >
                                    {{
                                        absensi
                                            .pendaftaran
                                            ?.peserta
                                            ?.nama ?? '-'
                                    }}
                                </p>

                                <p
                                    class="mt-1 text-xs text-slate-400 dark:text-slate-500"
                                >
                                    ID Peserta:
                                    {{
                                        absensi
                                            .pendaftaran
                                            ?.peserta
                                            ?.id ?? '-'
                                    }}
                                </p>

                            </div>
                        </td>


                        <!-- PELATIHAN -->
                        <td class="px-5 py-4">
                            <p
                                class="font-medium text-slate-700 dark:text-slate-200"
                            >
                                {{
                                    absensi
                                        .jadwal
                                        ?.program_pelatihan
                                        ?.pelatihan
                                        ?.nama ?? '-'
                                }}
                            </p>
                        </td>


                        <!-- TAHAP -->
                        <td class="px-5 py-4 text-center">

                            <span
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300"
                            >
                                {{
                                    absensi
                                        .jadwal
                                        ?.program_pelatihan
                                        ?.tahap
                                        ?.nama ?? '-'
                                }}
                            </span>

                        </td>


                        <!-- KELAS -->
                        <td class="px-5 py-4">

                            <span
                                class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                {{
                                    absensi
                                        .pendaftaran
                                        ?.kelas
                                        ?.nama ?? '-'
                                }}
                            </span>

                        </td>


                        <!-- PERTEMUAN -->
                        <td
                            class="px-5 py-4 text-center text-sm text-slate-600 dark:text-slate-300"
                        >
                            <span class="whitespace-nowrap">
                                Pertemuan
                                {{
                                    absensi
                                        .jadwal
                                        ?.pertemuan ?? '-'
                                }}
                            </span>
                        </td>


                        <!-- WAKTU -->
                        <td class="px-5 py-4">

                            <p
                                class="whitespace-nowrap text-sm font-semibold text-slate-700 dark:text-slate-200"
                            >
                                {{
                                    formatTime(
                                        absensi.waktu_masuk
                                    )
                                }}
                            </p>

                            <p
                                class="mt-1 whitespace-nowrap text-xs text-slate-400 dark:text-slate-500"
                            >
                                {{
                                    formatTanggal(
                                        absensi.waktu_masuk
                                    )
                                }}
                            </p>

                        </td>


                        <!-- STATUS -->
                        <td class="px-5 py-4 text-center">

                            <span
                                class="inline-flex items-center justify-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold capitalize text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400"
                            >
                                {{
                                    absensi.status ?? 'hadir'
                                }}
                            </span>

                        </td>


                        <!-- METODE -->
                        <td class="px-5 py-4 text-center">

                            <span
                                v-if="absensi.metode === 'face'"
                                class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300"
                            >

                                <span class="text-[10px]">
                                    ●
                                </span>

                                Face

                            </span>

                            <span
                                v-else
                                class="text-xs font-medium capitalize text-slate-500 dark:text-slate-400"
                            >
                                {{
                                    absensi.metode ?? '-'
                                }}
                            </span>

                        </td>


                        <!-- AKSI -->
                        <td class="px-5 py-4 text-center">

                            <div class="flex items-center justify-center">

                                <button
                                    type="button"
                                    @click="deleteAbsensi(absensi)"
                                    class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-400 dark:hover:bg-red-950/50"
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h12"
                                        />
                                    </svg>

                                    Hapus

                                </button>

                            </div>

                        </td>

                    </tr>


                    <!-- EMPTY -->
                    <tr v-if="filteredAbsensis.length === 0">

                        <td
                            colspan="10"
                            class="px-5 py-16 text-center"
                        >

                            <div
                                class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700"
                            >

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
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z"
                                    />
                                </svg>

                            </div>

                            <p
                                class="mt-4 text-sm font-semibold text-slate-600 dark:text-slate-300"
                            >
                                Belum ada data absensi
                            </p>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Data peserta yang berhasil melakukan absensi akan muncul di sini.
                            </p>

                        </td>

                    </tr>

                </tbody>
                </table>


            </div>

        </div>

    </div>

</template>