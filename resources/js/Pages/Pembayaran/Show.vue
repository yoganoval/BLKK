<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pembayaran: {
        type: Object,
        required: true,
    },
})

const formatRupiah = (value) => {
    return Number(value || 0).toLocaleString('id-ID')
}

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
        case 'lunas':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'

        case 'pending':
            return 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'

        case 'ditolak':
            return 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400'

        default:
            return 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
    }
}
</script>

<template>
    <Head title="Detail Pembayaran" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">

        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <div class="mb-3 flex items-center gap-2 text-sm text-slate-500">

                        <Link
                            :href="route('pembayarans.index')"
                            class="hover:text-indigo-600"
                        >
                            Pembayaran
                        </Link>

                        <span>/</span>

                        <span>
                            Detail
                        </span>

                    </div>

                    <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                        Detail Pembayaran
                    </h1>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Informasi lengkap transaksi pembayaran
                    </p>

                </div>

                <div class="flex gap-2">

                    <Link
                        :href="route('pembayarans.index')"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                    >
                        Kembali
                    </Link>

                    <Link
                        :href="route('pembayarans.edit', pembayaran.id)"
                        class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Edit
                    </Link>

                </div>

            </div>

            <!-- PAYMENT SUMMARY -->
            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <div class="border-b border-slate-200 p-6 dark:border-slate-800">

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Jumlah Pembayaran
                            </p>

                            <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                Rp {{ formatRupiah(pembayaran.jumlah) }}
                            </p>

                            <p class="mt-2 text-sm text-slate-500">
                                Transaksi #{{ pembayaran.id }}
                            </p>

                        </div>

                        <span
                            class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold"
                            :class="statusClass(pembayaran.status)"
                        >
                            {{ pembayaran.status }}
                        </span>

                    </div>

                </div>

                <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-3">

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Tanggal Bayar
                        </p>

                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ formatTanggal(pembayaran.tanggal_bayar) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Metode
                        </p>

                        <p class="mt-1 font-semibold capitalize text-slate-700 dark:text-slate-200">
                            {{ pembayaran.metode || '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Dibuat
                        </p>

                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ formatDateTime(pembayaran.created_at) }}
                        </p>
                    </div>

                </div>

            </div>

            <!-- PENDAFTARAN -->
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <div class="mb-5 flex items-center gap-3">

                    <div class="rounded-xl bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm9 2a3 3 0 10-2.83-4"
                            />
                        </svg>

                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white">
                            Data Pendaftaran
                        </h3>

                        <p class="text-xs text-slate-500">
                            Peserta yang melakukan pembayaran
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                    <div>
                        <p class="text-xs text-slate-400">
                            Peserta
                        </p>

                        <p class="mt-1 font-semibold text-slate-800 dark:text-white">
                            {{ pembayaran.pendaftaran?.peserta?.nama || '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">
                            Pelatihan
                        </p>

                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ pembayaran.pendaftaran?.program_pelatihan?.pelatihan?.nama || '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">
                            Program
                        </p>

                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ pembayaran.pendaftaran?.program_pelatihan?.nama || '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">
                            Kelas
                        </p>

                        <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ pembayaran.pendaftaran?.kelas?.nama || '-' }}
                        </p>
                    </div>

                </div>

            </div>

            <!-- BUKTI -->
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <h3 class="font-bold text-slate-800 dark:text-white">
                    Bukti Pembayaran
                </h3>

                <div
                    v-if="pembayaran.bukti"
                    class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800"
                >

                    <a
                        :href="`/storage/${pembayaran.bukti}`"
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
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
                                stroke-width="1.8"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>

                        Lihat Bukti Pembayaran

                    </a>

                </div>

                <p
                    v-else
                    class="mt-4 text-sm text-slate-500"
                >
                    Belum ada bukti pembayaran.
                </p>

            </div>

            <!-- KETERANGAN -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <h3 class="font-bold text-slate-800 dark:text-white">
                    Keterangan
                </h3>

                <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                    {{ pembayaran.keterangan || 'Tidak ada keterangan.' }}
                </p>

            </div>

        </div>

    </div>
</template>