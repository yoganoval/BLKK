<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    lpj: {
        type: Array,
        default: () => [],
    },
})

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const search = ref('')
const statusFilter = ref('semua')

/*
|--------------------------------------------------------------------------
| FORMAT
|--------------------------------------------------------------------------
*/

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(value || 0))
}

const formatTanggal = (tanggal) => {
    if (!tanggal) {
        return '-'
    }

    const date = new Date(tanggal)

    if (Number.isNaN(date.getTime())) {
        return tanggal
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date)
}

const formatStatus = (status) => {
    const statusMap = {
        draft: 'Draft',
        diajukan: 'Diajukan',
        disetujui: 'Disetujui',
        ditolak: 'Ditolak',
        selesai: 'Selesai',
    }

    return statusMap[status] || status
}

const statusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
        diajukan: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        disetujui: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        ditolak: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        selesai: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
    }

    return classes[status] || classes.draft
}

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

const filteredLpj = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    return props.lpj.filter((item) => {
        const matchesSearch =
            !keyword ||
            item.kode_lpj?.toLowerCase().includes(keyword) ||
            item.keterangan?.toLowerCase().includes(keyword)

        const matchesStatus =
            statusFilter.value === 'semua' ||
            item.status === statusFilter.value

        return matchesSearch && matchesStatus
    })
})

/*
|--------------------------------------------------------------------------
| SUMMARY
|--------------------------------------------------------------------------
*/

const totalLpj = computed(() => props.lpj.length)

const totalPemasukan = computed(() => {
    return props.lpj.reduce(
        (total, item) =>
            total + Number(item.total_pemasukan || 0),
        0
    )
})

const totalPengeluaran = computed(() => {
    return props.lpj.reduce(
        (total, item) =>
            total + Number(item.total_pengeluaran || 0),
        0
    )
})

const totalSaldo = computed(() => {
    return totalPemasukan.value - totalPengeluaran.value
})

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deleteLpj = (item) => {
    if (
        !confirm(
            `Hapus ${item.kode_lpj}? Semua data pengeluaran dan bukti di dalam LPJ ini juga akan dihapus.`
        )
    ) {
        return
    }

    router.delete(
        route('lpj-keuangan.destroy', item.id)
    )
}
</script>

<template>
    <Head title="LPJ Keuangan" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-gray-900 dark:text-white"
                >
                    LPJ Keuangan
                </h1>

                <p
                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                >
                    Pertanggungjawaban keuangan kas BLKK
                </p>
            </div>

            <Link
                :href="route('lpj-keuangan.create')"
                class="inline-flex w-fit items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
                <span class="text-lg leading-none">+</span>
                Buat LPJ
            </Link>
        </div>


        <!-- SUMMARY -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

            <!-- TOTAL LPJ -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                            Total LPJ
                        </p>

                        <p
                            class="mt-2 text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ totalLpj }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-xl dark:bg-blue-900/30"
                    >
                        📋
                    </div>
                </div>
            </div>


            <!-- PEMASUKAN -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                            Total Pemasukan
                        </p>

                        <p
                            class="mt-2 text-xl font-bold text-green-600 dark:text-green-400"
                        >
                            {{ formatRupiah(totalPemasukan) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-xl dark:bg-green-900/30"
                    >
                        💰
                    </div>
                </div>
            </div>


            <!-- PENGELUARAN -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                            Total Pengeluaran
                        </p>

                        <p
                            class="mt-2 text-xl font-bold text-red-600 dark:text-red-400"
                        >
                            {{ formatRupiah(totalPengeluaran) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-xl dark:bg-red-900/30"
                    >
                        💸
                    </div>
                </div>
            </div>


            <!-- SALDO -->
            <div
                class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm dark:border-blue-900/50 dark:bg-blue-900/20"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-blue-700 dark:text-blue-300"
                        >
                            Total Saldo
                        </p>

                        <p
                            class="mt-2 text-xl font-bold"
                            :class="
                                totalSaldo >= 0
                                    ? 'text-blue-700 dark:text-blue-300'
                                    : 'text-red-600 dark:text-red-400'
                            "
                        >
                            {{ formatRupiah(totalSaldo) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-xl dark:bg-blue-900/30"
                    >
                        🏦
                    </div>
                </div>
            </div>
        </div>


        <!-- TABLE -->
        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >

            <!-- FILTER -->
            <div
                class="flex flex-col gap-4 border-b border-gray-200 p-5 md:flex-row md:items-center md:justify-between dark:border-gray-700"
            >
                <div class="relative w-full md:max-w-sm">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                    >
                        🔍
                    </span>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari kode LPJ..."
                        class="w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                    />
                </div>

                <select
                    v-model="statusFilter"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200"
                >
                    <option value="semua">
                        Semua Status
                    </option>

                    <option value="draft">
                        Draft
                    </option>

                    <option value="diajukan">
                        Diajukan
                    </option>

                    <option value="disetujui">
                        Disetujui
                    </option>

                    <option value="ditolak">
                        Ditolak
                    </option>

                    <option value="selesai">
                        Selesai
                    </option>
                </select>
            </div>


            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-gray-200 bg-gray-50 text-left dark:border-gray-700 dark:bg-gray-900/50"
                        >
                            <th
                                class="whitespace-nowrap px-5 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >
                                No
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Kode LPJ
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Periode
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Pemasukan
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Pengeluaran
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Saldo
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 text-center font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Status
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-4 text-center font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        class="divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="(item, index) in filteredLpj"
                            :key="item.id"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700/30"
                        >
                            <td
                                class="whitespace-nowrap px-5 py-4 text-gray-500 dark:text-gray-400"
                            >
                                {{ index + 1 }}
                            </td>

                            <td class="px-5 py-4">
                                <Link
                                    :href="
                                        route(
                                            'lpj-keuangan.show',
                                            item.id
                                        )
                                    "
                                    class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400"
                                >
                                    {{ item.kode_lpj }}
                                </Link>
                            </td>

                            <td
                                class="whitespace-nowrap px-5 py-4 text-gray-700 dark:text-gray-300"
                            >
                                {{ formatTanggal(item.periode_mulai) }}
                                -
                                {{ formatTanggal(item.periode_selesai) }}
                            </td>

                            <td
                                class="whitespace-nowrap px-5 py-4 text-right font-medium text-green-600 dark:text-green-400"
                            >
                                {{ formatRupiah(item.total_pemasukan) }}
                            </td>

                            <td
                                class="whitespace-nowrap px-5 py-4 text-right font-medium text-red-600 dark:text-red-400"
                            >
                                {{ formatRupiah(item.total_pengeluaran) }}
                            </td>

                            <td
                                class="whitespace-nowrap px-5 py-4 text-right font-semibold"
                                :class="
                                    Number(item.saldo || 0) >= 0
                                        ? 'text-blue-600 dark:text-blue-400'
                                        : 'text-red-600 dark:text-red-400'
                                "
                            >
                                {{ formatRupiah(item.saldo) }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="statusClass(item.status)"
                                >
                                    {{ formatStatus(item.status) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div
                                    class="flex items-center justify-center gap-1"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'lpj-keuangan.show',
                                                item.id
                                            )
                                        "
                                        class="rounded-lg p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20"
                                        title="Detail"
                                    >
                                        👁️
                                    </Link>

                                    <Link
                                        v-if="
                                            item.status === 'draft' ||
                                            item.status === 'ditolak'
                                        "
                                        :href="
                                            route(
                                                'lpj-keuangan.edit',
                                                item.id
                                            )
                                        "
                                        class="rounded-lg p-2 text-yellow-600 hover:bg-yellow-50 dark:text-yellow-400 dark:hover:bg-yellow-900/20"
                                        title="Edit"
                                    >
                                        ✏️
                                    </Link>

                                    <button
                                        v-if="item.status === 'draft'"
                                        type="button"
                                        @click="deleteLpj(item)"
                                        class="rounded-lg p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                        title="Hapus"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>


                        <!-- EMPTY -->
                        <tr v-if="filteredLpj.length === 0">
                            <td
                                colspan="8"
                                class="px-5 py-14 text-center"
                            >
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-2xl dark:bg-gray-700"
                                >
                                    📋
                                </div>

                                <h3
                                    class="mt-4 font-semibold text-gray-900 dark:text-white"
                                >
                                    Belum ada LPJ
                                </h3>

                                <p
                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Belum ada data LPJ yang sesuai.
                                </p>

                                <Link
                                    :href="
                                        route(
                                            'lpj-keuangan.create'
                                        )
                                    "
                                    class="mt-4 inline-flex rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                                >
                                    + Buat LPJ
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</template>



