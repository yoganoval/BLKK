<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pembayarans: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')
const statusFilter = ref('')

const filteredPembayarans = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    return props.pembayarans.filter((item) => {
        const peserta =
            item.pendaftaran?.peserta?.nama?.toLowerCase() || ''

        const program =
            item.pendaftaran?.program_pelatihan?.nama?.toLowerCase() || ''

        const pelatihan =
            item.pendaftaran?.program_pelatihan?.pelatihan?.nama?.toLowerCase() || ''

        const metode =
            item.metode?.toLowerCase() || ''

        const matchesSearch =
            !keyword ||
            peserta.includes(keyword) ||
            program.includes(keyword) ||
            pelatihan.includes(keyword) ||
            metode.includes(keyword)

        const matchesStatus =
            !statusFilter.value ||
            item.status === statusFilter.value

        return matchesSearch && matchesStatus
    })
})

const totalLunas = computed(() => {
    return props.pembayarans.filter(item => item.status === 'lunas').length
})

const totalPending = computed(() => {
    return props.pembayarans.filter(item => item.status === 'pending').length
})

const totalDitolak = computed(() => {
    return props.pembayarans.filter(item => item.status === 'ditolak').length
})

const totalNominalLunas = computed(() => {
    return props.pembayarans
        .filter(item => item.status === 'lunas')
        .reduce((total, item) => {
            return total + Number(item.jumlah || 0)
        }, 0)
})

const formatRupiah = (value) => {
    return Number(value || 0).toLocaleString('id-ID')
}

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

const statusClass = (status) => {
    switch (status) {
        case 'lunas':
            return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400'

        case 'pending':
            return 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400'

        case 'ditolak':
            return 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400'

        default:
            return 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-400'
    }
}

const deletePembayaran = (id) => {
    if (!confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')) {
        return
    }

    router.delete(route('pembayarans.destroy', id))
}
</script>

<template>
    <Head title="Pembayaran" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <div class="mb-2 flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
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
                                    stroke-width="1.8"
                                    d="M3 10h18M5 6h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2zm3 8h4"
                                />
                            </svg>
                        </div>

                        <div>
                            <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                                Pembayaran
                            </h1>

                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Kelola pembayaran peserta pelatihan
                            </p>
                        </div>

                    </div>
                </div>

                <Link
                    :href="route('pembayarans.create')"
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
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    Tambah Pembayaran
                </Link>

            </div>

            <!-- STATISTIK -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Total Pembayaran
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-800 dark:text-white">
                        {{ pembayarans.length }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Lunas
                    </p>

                    <p class="mt-1 text-2xl font-bold text-emerald-600">
                        {{ totalLunas }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Pending
                    </p>

                    <p class="mt-1 text-2xl font-bold text-amber-600">
                        {{ totalPending }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Total Nominal Lunas
                    </p>

                    <p class="mt-1 text-xl font-bold text-indigo-600 dark:text-indigo-400">
                        Rp {{ formatRupiah(totalNominalLunas) }}
                    </p>
                </div>

            </div>

            <!-- TABLE -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

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
                                    stroke-width="1.8"
                                    d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"
                                />
                            </svg>

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari peserta, program, pelatihan, atau metode..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            />

                        </div>

                        <select
                            v-model="statusFilter"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >
                            <option value="">
                                Semua Status
                            </option>

                            <option value="pending">
                                Pending
                            </option>

                            <option value="lunas">
                                Lunas
                            </option>

                            <option value="ditolak">
                                Ditolak
                            </option>
                        </select>

                    </div>
                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left">

                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/50">

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    #
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Peserta
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Program
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Jumlah
                                </th>

                                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Tanggal
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Aksi
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                            <tr
                                v-for="(item, index) in filteredPembayarans"
                                :key="item.id"
                                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                            >

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">
                                    {{ index + 1 }}
                                </td>

                                <td class="px-5 py-4">

                                    <div class="font-semibold text-slate-800 dark:text-white">
                                        {{ item.pendaftaran?.peserta?.nama || '-' }}
                                    </div>

                                    <div class="mt-0.5 text-xs text-slate-500">
                                        ID Pendaftaran #{{ item.pendaftaran_id }}
                                    </div>

                                </td>

                                <td class="px-5 py-4">

                                    <div class="font-medium text-slate-700 dark:text-slate-200">
                                        {{ item.pendaftaran?.program_pelatihan?.nama || '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ item.pendaftaran?.program_pelatihan?.pelatihan?.nama || '-' }}
                                    </div>

                                </td>

                                <td class="whitespace-nowrap px-5 py-4">

                                    <span class="font-bold text-slate-800 dark:text-white">
                                        Rp {{ formatRupiah(item.jumlah) }}
                                    </span>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ item.metode || 'Metode belum diisi' }}
                                    </div>

                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ formatTanggal(item.tanggal_bayar) }}
                                </td>

                                <td class="px-5 py-4 text-center">

                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset"
                                        :class="statusClass(item.status)"
                                    >
                                        {{ item.status }}
                                    </span>

                                </td>

                                <td class="px-5 py-4">

                                    <div class="flex justify-center gap-2">

                                        <Link
                                            :href="route('pembayarans.show', item.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-indigo-400"
                                        >
                                            Detail
                                        </Link>

                                        <Link
                                            :href="route('pembayarans.edit', item.id)"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-indigo-400"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            type="button"
                                            @click="deletePembayaran(item.id)"
                                            class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900/50 dark:bg-slate-800 dark:text-red-400 dark:hover:bg-red-500/10"
                                        >
                                            Hapus
                                        </button>

                                    </div>

                                </td>

                            </tr>

                            <tr v-if="filteredPembayarans.length === 0">
                                <td colspan="7" class="px-5 py-14 text-center">

                                    <div class="flex flex-col items-center">

                                        <div class="mb-3 rounded-2xl bg-slate-100 p-4 text-slate-400 dark:bg-slate-800">
                                            <svg
                                                class="h-8 w-8"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M3 10h18M5 6h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"
                                                />
                                            </svg>
                                        </div>

                                        <p class="font-semibold text-slate-700 dark:text-slate-200">
                                            Tidak ada data pembayaran
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Data pembayaran belum ditemukan.
                                        </p>

                                    </div>

                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</template>